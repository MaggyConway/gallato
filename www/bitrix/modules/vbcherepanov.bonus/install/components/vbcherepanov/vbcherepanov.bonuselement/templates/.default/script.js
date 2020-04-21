(function (window) {

if (!!window.ITRElementBonus)
{
	return;
}
window.ITRElementBonus = function (arParams)
{
	this.Update = {
		path: '/bitrix/components/vbcherepanov/vbcherepanov.bonuselement/ajax.php',
		params: {
			AJAX: 'Y',
			SITE_ID: arParams.siteid,
			PRODUCT_ID:arParams.productID,
			IBLOCK_ID:arParams.IBLOCKID,
            MIN_PRICE:arParams.MIN_PRICE,
            TYPE:arParams.TYPE,
			COUNTS :0,
		}
	};
};
window.ITRElementBonus.prototype.UpdateBonus = function(counts)
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
			   console.log(data);
               BX("bns").innerHTML=data;
           },
           onfailure: function(){

           }
    }); 
};
})(window);