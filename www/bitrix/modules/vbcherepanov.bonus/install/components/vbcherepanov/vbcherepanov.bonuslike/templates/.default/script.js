(function (window) {

    if (!!window.VBCHBBLIKE) {
        return;
    }
    var WindowBonus=function(title,content){
        var obPopupWin;
        if (!!obPopupWin)
            return;
        obPopupWin = BX.PopupWindowManager.create('BigBonusLikeSocial', null, {
            autoHide: false,
            offsetLeft: 0,
            offsetTop: 0,
            overlay : true,
            closeByEsc: true,
            titleBar: true,
            closeIcon: {top: '10px', right: '10px'}
        });
         obPopupWin.setTitleBar(title);
        obPopupWin.setContent(content);
        return obPopupWin;
    };
window.VBCHBBLIKE = function (arParams) {
    if(typeof arParams === 'object'){
        this.params=arParams;
        this.timesnap=0;
        this.obPopupWin = null;
        this.popupContent="";
        this.popupTitle="";
    }
};
    window.VBCHBBLIKE.prototype.callback=function(type,postid){
        BX.ajax({
            url: this.params.ADDBONUS,
            data: {'BBLIKEAJAX':'Y', 'SITEID':BX.message('SITE_ID'),'TYPE':type,'POSTID':postid,'URL':this.params.URL},
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: false,
            processData: true,
            scriptsRunFirst: true,
            emulateOnload: true,
            start: true,
            cache: false,
            onsuccess:function(data){
                var popupTitle,popupContent;
                if(data.OK==1){
                    popupTitle = {
                        content: BX.create('div', {
                            style: { marginRight: '30px', whiteSpace: 'nowrap' },
                            text: ('Add bonus')
                        })
                    };
                    popupContent = '<div style="width: 96%; margin: 10px 2%; text-align: center;"><p>'+BX.message('ADD_BONUS_MESSAGE')+data.BONUS+'</p></div>';
                }
                var l=WindowBonus(popupTitle,popupContent);
                l.show();
            },
            onfailure: function(){

            }
        });
    };
    window.VBCHBBLIKE.prototype.popup = function(url) {
        window.open(url,'','toolbar=0,status=0,width=626,height=436');
        return false;
    }
    window.VBCHBBLIKE.prototype.VK=function(){
        var _this=this;
        clearInterval(this.timesnap);
        VK.Api.call('wall.post', {'message': this.params.DESCRIPTION['VK']}, function(r) {
            _this.callback('VK',r.response.post_id);
        });
    };
    window.VBCHBBLIKE.prototype.FB=function(){
        var _this=this;
        clearInterval(this.timesnap);
        FB.ui({
                method: 'feed',
                name: this.title,
                caption: this.params.DESCRIPTION['FB'],
                description: this.params.DESCRIPTION['FB'],
                link: this.params.URL,
                properties: '',
                picture: this.image
            },
            function(response) {
                if (response && response.post_id) {
                    alert(BX.message('ADD_PUBLIC'));
                    _this.callback('FB',response.post_id);
                } else {
                    alert(BX.message('ERROR_PUBLIC'));
                }
            });
    };
    window.VBCHBBLIKE.prototype.inittwitter=function(intent_event){
        var _this=this;
        if (intent_event && intent_event.type == 'tweet' || intent_event.type == 'click') {
            var socialAction = intent_event.type + ((intent_event.type == 'click') ? '-' + intent_event.region : '');
            if(socialAction =='tweet')
            {
                 _this.callback('TW','');
            }
        }
    };
    window.VBCHBBLIKE.prototype.TW=function(){
        clearInterval(this.timesnap);
        url  = 'http://twitter.com/share?';
        url += 'text='      + encodeURIComponent(this.params.DESCRIPTION['FB']);
        url += '&url='      + encodeURIComponent(this.params.URL);
        url += '&counturl=' + encodeURIComponent(this.params.URL);
        url += 'original_referer' + encodeURIComponent(this.params.URL);
        this.popup(url)
    };
})(window);