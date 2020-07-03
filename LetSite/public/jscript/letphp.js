/*
* definimos algunas funciones de comunes de 
* PHP en Javascript
* 
*/

function getConfig(sConfig)
{
	return $aLetParams[sConfig];
}


function letphp_empty(mValue) {
	var key;
    
	if(mValue === "" || mValue === 0 || mValue === "0" || mValue === null || mValue === false || mValue === undefined || letphp_trim(mValue) == "" )
    {
			return true;
    }
    
	if(typeof mValue == 'object') 
	{
		for(key in mValue) 
		{
			if (typeof mValue[key] !== 'function' ) 
			{
				return false;
			}
		}
        return true;
	}
	return false;
}


function letphp_trim(sString, sCharlist) { 
	var whitespace, l = 0, i = 0;
	sString += '';
    
	if (!sCharlist) 
	{
		whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
  } 
  else 
  {
		sCharlist += '';
		whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
  }
    
	l = sString.length;
	for (i = 0; i < l; i++) 
	{
		if (whitespace.indexOf(sString.charAt(i)) === -1) 
		{
			sString = sString.substring(i);
			break;
    }
  }
    
	l = sString.length;
	for (i = l - 1; i >= 0; i--) 
	{
		if (whitespace.indexOf(sString.charAt(i)) === -1) 
		{
			sString = sString.substring(0, i + 1);
			break;
    }
  }
    
	return whitespace.indexOf(sString.charAt(0)) === -1 ? sString : '';
}


function letphp_ltrim(str, charlist) 
{ 
	charlist = !charlist ? ' \s\xA0' : (charlist+'').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
	var re = new RegExp('^[' + charlist + ']+', 'g');
	return (str+'').replace(re, '');
}

function letphp_rtrim(str, charlist ) 
{ 
    charlist = !charlist ? ' \s\xA0' : (charlist+'').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
    var re = new RegExp('[' + charlist + ']+$', 'g');
    return (str+'').replace(re, '');
}


function letphp_str_repeat(string, repeat) 
{
	var sOutput = '';
  for(var i = 0; i < repeat; i++) 
  {
    sOutput += string;
  }
  return sOutput;
}

function letphp_debug(mValues)
{
	console.log(mValues);
}


function letphp_isset() 
{
    var a=arguments; var l=a.length; var i=0;
    
    if (l==0) { 
	        throw new Error('Empty isset'); 
    }
    
    while (i!=l) {
        if (typeof(a[i])=='undefined' || a[i]===null) { 
            return false; 
        } else { 
            i++; 
        }
    }
    return true;
}