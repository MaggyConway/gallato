(function (window) {

if (!!window.ITRCartBonus)
{
	return;
}
window.ITRCartBonus = function (arParams)
{
	this.Update = {
		path: '/bitrix/components/vbcherepanov/vbcherepanov.bonusfororder/ajax.php',
		params: {
			AJAX: 'Y',
			SITE_ID: arParams.siteid,
			TYPE:arParams.TYPE,
		}
	};
};
window.ITRCartBonus.prototype.UpdateBonus = function(counts)
{
	this.Update.params.COUNTS=counts;
	BX.ajax({
           url: this.Update.path,
           data: this.Update.params,
           method: 'POST',
           dataType: 'html',
           timeout: 30,
           async: true,
           processData: true,
           scriptsRunFirst: true,
           emulateOnload: true,
           start: true,
           cache: false,
           onsuccess: function(data){
				BX('bns').innerHTML=data;
           },
           onfailure: function(){

           }
    }); 
};
})(window);