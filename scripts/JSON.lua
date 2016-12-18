local OBJDEF={}
local isArray={__tostring=function() return "JSON array" end} isArray.__index=isArray
local isObject={__tostring=function() return "JSON object" end} isObject.__index=isObject
function OBJDEF:newArray(tbl)
   return setmetatable(tbl or {},isArray)
end
function OBJDEF:newObject(tbl)
   return setmetatable(tbl or {},isObject)
end
local function unicode_codepoint_as_utf8(codepoint)
   if codepoint<=127 then
      return string.char(codepoint)
   elseif codepoint<=2047 then
      local highpart=math.floor(codepoint/0x40)
      local lowpart=codepoint-(0x40*highpart)
      return string.char(0xC0+highpart,0x80+lowpart)
   elseif codepoint<=65535 then
      local highpart=math.floor(codepoint/0x1000)
      local remainder=codepoint-0x1000*highpart
      local midpart=math.floor(remainder/0x40)
      local lowpart=remainder-0x40*midpart
      highpart=0xE0+highpart
      midpart=0x80+midpart
      lowpart=0x80+lowpart
      if(highpart==0xE0 and midpart<0xA0)or(highpart==0xED and midpart>0x9F)or(highpart==0xF0 and midpart<0x90)or(highpart==0xF4 and midpart>0x8F)
      then return "?"
      else return string.char(highpart,midpart,lowpart)
      end
   else
      local highpart=math.floor(codepoint/0x40000)
      local remainder=codepoint-0x40000*highpart
      local midA=math.floor(remainder/0x1000)
      remainder=remainder-0x1000*midA
      local midB=math.floor(remainder/0x40)
      local lowpart=remainder-0x40*midB
      return string.char(0xF0+highpart,0x80+midA,0x80+midB,0x80+lowpart)
   end
end
function OBJDEF:onEncodeError(message,etc)
   if etc~=nil then
      message=message .. " (" .. OBJDEF:encode(etc) .. ")"
   end

   if self.assert then
      self.assert(false,message)
   else
      assert(false,message)
   end
end
local function backslash_replacement_function(c)
   if c=="\n" then return "\\n"
   elseif c=="\r" then return "\\r"
   elseif c=="\t" then return "\\t"
   elseif c=="\b" then return "\\b"
   elseif c=="\f" then return "\\f"
   elseif c=='"' then return '\\"'
   elseif c=='\\' then return '\\\\'
   else return string.format("\\u%04x",c:byte())
   end
end
local chars_to_be_escaped_in_JSON_string
   = '['
   ..    '"'
   ..    '%\\'
   ..    '%z'
   ..    '\001' .. '-' .. '\031'
   .. ']'
local function json_string_literal(value)
   local newval=value:gsub(chars_to_be_escaped_in_JSON_string,backslash_replacement_function)
   return '"' .. newval .. '"'
end
local function object_or_array(self,T,etc)
   local string_keys={}
   local number_keys={}
   local number_keys_must_be_strings=false
   local maximum_number_key
   for key in pairs(T) do
      if type(key)=='string' then table.insert(string_keys,key)
      elseif type(key)=='number' then table.insert(number_keys,key)
         if key<=0 or key>=math.huge then number_keys_must_be_strings=true
         elseif not maximum_number_key or key>maximum_number_key then maximum_number_key=key
         end
      else
         self:onEncodeError("can't encode table with a key of type " .. type(key),etc)
      end
   end
   if #string_keys==0 and not number_keys_must_be_strings then
      if #number_keys>0 then return nil, maximum_number_key
      elseif tostring(T)=="JSON array" then return nil
      elseif tostring(T)=="JSON object" then return {}
      else return nil
      end
   end
   table.sort(string_keys)
   local map
   if #number_keys>0 then
      if self.noKeyConversion then
         self:onEncodeError("a table with both numeric and string keys could be an object or array; aborting",etc)
      end
      map={}
      for key,val in pairs(T) do
         map[key]=val
      end
      table.sort(number_keys)
      for _, number_key in ipairs(number_keys) do
         local string_key=tostring(number_key)
         if map[string_key]==nil then
            table.insert(string_keys,string_key)
            map[string_key]=T[number_key]
         else
            self:onEncodeError("conflict converting table with mixed-type keys into a JSON object: key " .. number_key .. " exists both as a string and a number.", etc)
         end
      end
   end
   return string_keys,nil,map
end
local encode_value
function encode_value(self,value,parents,etc,options,indent)
   if value==nil then return 'null'
   elseif type(value)=='string' then return json_string_literal(value)
   elseif type(value)=='number' then
      if value~=value then return "null"
      elseif value>=math.huge then return "1e+9999"
      elseif value<=-math.huge then return "-1e+9999"
      else return tostring(value)
      end
   elseif type(value)=='boolean' then return tostring(value)
   elseif type(value)~='table' then self:onEncodeError("can't convert " .. type(value) .. " to JSON",etc)
   else
      local T=value
      if type(options)~='table' then options={}
      end
      if type(indent)~='string' then indent=""
      end
      if parents[T] then self:onEncodeError("table " .. tostring(T) .. " is a child of itself",etc)
      else parents[T] = true
      end
      local result_value
      local object_keys, maximum_number_key, map = object_or_array(self,T,etc)
      if maximum_number_key then
         local ITEMS={}
         for i=1,maximum_number_key do table.insert(ITEMS, encode_value(self,T[i],parents,etc,options,indent))
         end
         if options.pretty then result_value = "[ " .. table.concat(ITEMS, ", ") .. " ]"
         else result_value = "["  .. table.concat(ITEMS, ",")  .. "]"
         end
      elseif object_keys then
         local TT=map or T
         if options.pretty then
            local KEYS={}
            local max_key_length=0
            for _,key in ipairs(object_keys) do
               local encoded=encode_value(self,tostring(key),parents,etc,options,indent)
               if options.align_keys then max_key_length=math.max(max_key_length,#encoded)
               end
               table.insert(KEYS,encoded)
            end
            local key_indent=indent .. tostring(options.indent or "")
            local subtable_indent=key_indent .. string.rep(" ",max_key_length) .. (options.align_keys and "  " or "")
            local FORMAT="%s%" .. string.format("%d",max_key_length) .. "s: %s"

            local COMBINED_PARTS={}
            for i,key in ipairs(object_keys) do
               local encoded_val=encode_value(self,TT[key],parents,etc,options,subtable_indent)
               table.insert(COMBINED_PARTS,string.format(FORMAT,key_indent,KEYS[i],encoded_val))
            end
            result_value="{\n" .. table.concat(COMBINED_PARTS,",\n") .. "\n" .. indent .. "}"
         else
            local PARTS={}
            for _,key in ipairs(object_keys) do
               local encoded_val=encode_value(self,TT[key],parents,etc,options,indent)
               local encoded_key=encode_value(self,tostring(key),parents,etc,options,indent)
               table.insert(PARTS,string.format("%s:%s",encoded_key,encoded_val))
            end
            result_value="{" .. table.concat(PARTS,",") .. "}"
         end
      else
         result_value="[]"
      end
      parents[T]=false
      return result_value
   end
end
function OBJDEF:encode(value,etc,options)
   if type(self)~='table' or self.__index~=OBJDEF then OBJDEF:onEncodeError("JSON:encode must be called in method format", etc)
   end
   return encode_value(self,value,{},etc,options or nil)
end
function OBJDEF.__tostring()
   return "JSON encode/decode package"
end
OBJDEF.__index=OBJDEF
function OBJDEF:new(args)
   local new={}
   if args then
      for key, val in pairs(args) do
         new[key] = val
      end
   end
   return setmetatable(new,OBJDEF)
end
return OBJDEF:new()