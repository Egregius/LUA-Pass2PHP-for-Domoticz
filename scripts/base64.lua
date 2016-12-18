local known_base64_alphabets={base64={_alpha="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",_strip="[^%a%d%+%/%=]",_term="="},base64noterm={_alpha="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",_strip="[^%a%d%+%/]",_term=""},base64url={_alpha="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_",_strip="[^%a%d%+%-%_=]",_term=""},}
local c_alpha=known_base64_alphabets.base64
local pattern_strip
local b64e
local b64e_a
local b64e_a2
local b64e_b1
local b64e_b2
local b64e_c1
local b64e_c
local tail_padd64={"==","="}
local function e64(a,b,c)
    return  b64e_a[a],b64e[b64e_a2[a]+b64e_b1[b]],b64e[b64e_b2[b]+b64e_c1[c]],b64e_c[c]
end
local function encode_tail64(out,x,y)
    if x~=nil then
        local a,b,r=x,0,1
        if y~=nil then
            r=2
            b=y
        end
		local b1,b2,b3=e64(a,b,0)
		local tail_value=string.char(b1,b2)
        if r==2 then
            tail_value=tail_value..string.char(b3)
        end
        out(tail_value .. tail_padd64[r])
    end
end
local function encode64_io_iterator(file)
    assert(io.type(file)=="file","argument must be readable file handle")
    assert(file.read~=nil,"argument must be readable file handle")
    local ii={}
    setmetatable(ii,{__tostring=function() return "base64.io_iterator" end})
    function ii.begin()
        local sb=string.byte
        return function()
            s=file:read(3)
            if s~=nil and #s==3 then
                return sb(s,1,3)
            end
            return nil
        end
    end
    function ii.tail()
        if s~=nil then return s:byte(1,2) end
    end
    return ii
end
local function encode64_with_ii(ii,out)
    local sc=string.char
    for a,b,c in ii.begin() do
        out(sc(e64(a,b,c)))
    end
    encode_tail64(out,ii.tail())
end
local function encode64_with_predicate(raw,out)
    local rem=#raw%3
    local len=#raw-rem
    local sb=string.byte
    local sc=string.char
    for i=1,len,3 do
        out(sc(e64(sb(raw,i,i+3))))
    end
    if rem>0 then
        local x,y=sb(raw,len+1)
        if rem>1 then
            y=sb(raw,len+2)
        end
        encode_tail64(out,x,y)
    end
end
local function encode64_tostring(raw)
    local sb={}
    local function collection_predicate(v)
        sb[#sb+1]=v
    end
    encode64_with_predicate(raw,collection_predicate)
    return table.concat(sb)
end
local b64d
local b64d_a1
local b64d_a2
local b64d_b1
local b64d_b2
local b64d_c1
local b64d_z
local function d64(b1,b2,b3,b4)
    return b64d_a1[b1]+b64d_a2[b2],b64d_b1[b2]+b64d_b2[b3],b64d_c1[b3]+b64d[b4]
end
local function set_and_get_alphabet(alpha,term)
    if alpha~=nil then
        local magic={[" "]="% ",["^"]="%^",["$"]="%$",["("]="%(",[")"]="%)",["."]="%.",["["]="%[",["]"]="%]",["*"]="%*",["+"]="%+",["-"]="%-",["?"]="%?",}
        c_alpha=known_base64_alphabets[alpha]
        if c_alpha==nil then
            c_alpha={_alpha=alpha,_term=term }
        end
        assert(#c_alpha._alpha==64,"The alphabet ~must~ be 64 unique values.")
        assert(#c_alpha._term<=1,"Specify zero or one termination character.")
        b64d={}
        b64e={}
        local s=""
        for i = 1,64 do
            local byte=c_alpha._alpha:byte(i)
            local str=string.char(byte)
            b64e[i-1]=byte
            assert(b64d[byte]==nil,"Duplicate value '"..str.."'")
            b64d[byte]=i-1
            s=s..str
        end
        local ext
        if bit32 then
            ext=bit32.extract
        elseif bit then
            local band=bit.band
            local rshift=bit.rshift
            ext=
                function(n,field,width)
                    width=width or 1
                    return band(rshift(n,field),2^width-1)
                end
        else
            error("Neither Lua 5.2 bit32 nor LuaJit bit library found!")
        end
        b64e_a={}
        b64e_a2={}
        b64e_b1={}
        b64e_b2={}
        b64e_c1={}
        b64e_c={}
        for f=0,255 do
            b64e_a [f]=b64e[ext(f,2,6)]
            b64e_a2 [f]=ext(f,0,2)*16
            b64e_b1 [f]=ext(f,4,4)
            b64e_b2 [f]=ext(f,0,4)*4
            b64e_c1 [f]=ext(f,6,2)
            b64e_c [f]=b64e[ext(f,0,6)]
        end
        b64d_a1={}
        b64d_a2={}
        b64d_b1={}
        b64d_b2={}
        b64d_c1={}
        b64d_z=b64e[0]
        for k,v in pairs(b64d) do
            b64d_a1 [k]=v*4
            b64d_a2 [k]=math.floor(v/16)
            b64d_b1 [k]=ext(v,0,4)*16
            b64d_b2 [k]=math.floor(v/4)
            b64d_c1 [k]=ext(v,0,2)*64
        end
        if c_alpha._term~="" then
            tail_padd64[1]=string.char(c_alpha._term:byte(),c_alpha._term:byte())
            tail_padd64[2]=string.char(c_alpha._term:byte())
        else
            tail_padd64[1]=""
            tail_padd64[2]=""
        end
        local esc_term
        if magic[c_alpha._term]~=nil then
            esc_term=c_alpha._term:gsub(magic[c_alpha._term],function (s) return magic[s] end)
        elseif c_alpha._term=="%" then
            esc_term="%%"
        else
            esc_term=c_alpha._term
        end
        if not c_alpha._strip then
            local p=s:gsub("%%",function (s) return "__unique__" end)
            for k,v in pairs(magic)
            do
                p=p:gsub(v,function (s) return magic[s] end )
            end
            local mr=p:gsub("__unique__",function() return "%%" end)
            c_alpha._strip = string.format("[^%s%s]",mr,esc_term)
        end
        assert(c_alpha._strip)
        pattern_strip = c_alpha._strip
        local c=0 for i in pairs(b64d) do c=c+1 end
        assert(c_alpha._alpha == s,"Integrity error.")
        assert(c == 64,"The alphabet must be 64 unique values.")
        if esc_term~="" then
            assert(not c_alpha._alpha:find(esc_term),"Tail characters must not exist in alphabet.")
        end
        if known_base64_alphabets[alpha]==nil then
            known_base64_alphabets[alpha]=c_alpha
        end
    end
    return c_alpha._alpha,c_alpha._term
end
local function encode64(i,o)
    local method
    if o~=nil and io.type(o)=="file" then
        local file_out = o
        o=function(s) file_out:write(s) end
    end
    if type(i)=="string" then
        if type(o)=="function" then
            method=encode64_with_predicate
        else
            assert(o==nil,"unsupported request")
            method=encode64_tostring
        end
    elseif io.type(i)=="file" then
        assert(type(o)=="function","file source requires output predicate")
        i=encode64_io_iterator(i)
        method=encode64_with_ii
    else
        assert(false,"unsupported mode")
    end
    return method(i,o)
end
set_and_get_alphabet("base64")
return {encode=encode64}