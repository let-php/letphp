let oLetPHPRequest = null;

window.onbeforeunload = function() 
{
	if (oLetPHPRequest !== null)
	{
		oLetPHPRequest.abort();
	}	
};

$.fn.LetPHPAjax = function(sApp, sAttrs, sType){
	
	if(letphp_empty(sType))
	{
		sType = 'POST';
	}
	
	var sRouteAjax = getConfig('sLtRouteAjax');
	var sParams = '&'+ getConfig('sLtGlobalTokenName')+ '[ajax]=true&'+ getConfig('sLtGlobalTokenName')+ '[call]='+ sApp;
	
	if(sAttrs)
	{
		sParams += '&' + letphp_ltrim(sAttrs, '&');
	}
	
	if (!sParams.match(/\[token\]/i))
	{
		sParams += '&' + getConfig('sLtGlobalTokenName') + '[token]=' + getConfig('sLtToken');
	}
	
	oLetPHPRequest = $.ajax({
		type: sType,
		url : sRouteAjax,
		dataType: 'script',
		data : sParams
	});
	
	return oLetPHPRequest;
	/*fetch(sRouteAjax, {
		method: sType,
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		body : sParams,
	})
	.then(res => res.text())
	.then(data => eval(data));*/
	
}

$.LetPHPAjax = function(sApp, sAttrs, sType)
{
	return $.fn.LetPHPAjax(sApp, sAttrs, sType);
};

// Con esta función traemos HTML atraves de Ajax
$LetPHP.ajaxHtml = async (sApp, sAttrs, sType, sElement) => 
{
	if(letphp_empty(sType))
	{
		sType = 'POST';
	}
	
	var sRouteAjax = getConfig('sLtRouteAjax');
	var sParams = '&'+ getConfig('sLtGlobalTokenName')+ '[ajax]=true&'+ getConfig('sLtGlobalTokenName')+ '[call]='+ sApp;
	
	if(sAttrs)
	{
		sParams += '&' + letphp_ltrim(sAttrs, '&');
	}
	
	if (!sParams.match(/\[token\]/i))
	{
		sParams += '&' + getConfig('sLtGlobalTokenName') + '[token]=' + getConfig('sLtToken');
	}
	$.ajax({
		type: sType,
		url : sRouteAjax,
		dataType: 'html',
		data : sParams,
		success: response => {
			$(sElement).html(response);
		}
	});
};


$LetPHP.ajaxMessage = msg => {
	const message = msg;
	if(message == '' )
	{
		message = 'Guardando...';
	}
	$('#let_ajax_message').html(message).animate({opacity: .9}).slideDown();
};
