(function(window){
    'use strict';

    if (window.ITRBonusIntegrate)
        return;

    window.ITRBonusIntegrate = function(arParams){
        if (typeof arParams === 'object') {
            this.Params=arParams;
            this.elementcount = 0;
            this.elementID = 0;
        }
        this.DetailElement();
        this.BasketAllBonus();
        if(BX.Sale)
            this.SOAAjax();
        BX.addCustomEvent('OnBasketChange',  BX.proxy(this.BasketAllBonus, this));
        BX.addCustomEvent('onCatalogElementChangeOffer',BX.proxy(this.DetailElement, this));
        BX.addCustomEvent('ChangeQuantity',  BX.proxy(this.ChangeQuantity, this));
        BX.addCustomEvent('onAjaxSuccess', BX.proxy(this.SOAAjax, this));
    };
    window.ITRBonusIntegrate.prototype = {
        BasketAllBonus:function(){
            if(!BX.Sale || !BX.Sale.BasketComponent)
                return;

            if(!BX.Sale.BasketComponent.result.GRID.ROWS && BX.Sale.BasketComponent.result.BASKET_ITEM_RENDER_DATA)
                BX.Sale.BasketComponent.result.GRID.ROWS = BX.Sale.BasketComponent.result.BASKET_ITEM_RENDER_DATA;
            if(!BX.Sale.BasketComponent.result.GRID.ROWS)
                return;
            var params= {
                AJAX: 'Y',
                SITE_ID: this.Params.SITE_ID,
                TYPE:'CART',
                ENDTOEND: 'Y',
                GRID: BX.Sale.BasketComponent.result.GRID.ROWS,
                COUNTS:1,
            };
            var _this=this;
            if(this.Params.CART.ON==true) {
                if(!document.getElementById('bonus_all')) {
                    var itog_block = BX.findChild(BX(_this.Params.CART.MAIN_BLOCK), {
                        'tag': 'div',
                        'class': _this.Params.CART.OUT_BLOCK
                    }, true);
                    var bonus_all = BX.create('DIV', {props: {id: 'bonus_all'}});
                    itog_block.appendChild(bonus_all);
                }

                BX.ajax({
                    url: this.Params.CART.PATH,
                    data: params,
                    method: 'POST',
                    dataType: 'json',
                    timeout: 30,
                    async: true,
                    processData: true,
                    scriptsRunFirst: true,
                    emulateOnload: true,
                    start: true,
                    cache: false,
                    onsuccess: function (data) {
                        if (parseFloat(data.ALLBONUS) > 0) {
                            var p=document.getElementById('bonus_all');
                            if (p) {
                                BX.adjust(BX('bonus_all'), {html: _this.Params.CART.LANG_BONUS + ' ' + data.ALLBONUS});
                            }
                            else{
                                var itog_block = BX.findChild(BX(_this.Params.CART.MAIN_BLOCK), {
                                    'tag': 'div',
                                    'class': _this.Params.CART.OUT_BLOCK
                                }, true);
                                var bonus_all = BX.create('DIV', {props: {id: 'bonus_all'}, html: _this.Params.CART.LANG_BONUS + ' ' + data.ALLBONUS});
                                itog_block.appendChild(bonus_all);
                            }

                        }
                        else {
                            BX.remove(BX('bonus_all'));
                        }
                        if (_this.Params.CART.BONUS_OFFER == true) {
                            for (var id in data.ITEMS) {
                                var item = data.ITEMS[id];
                                var product_block = BX('basket-item-' + item.ID);
                                var price_blocks = BX.findChild(product_block, {
                                    'tag': 'div',
                                    'class': 'basket-item-block-price'
                                }, true, true);
                                var price_for_item = price_blocks[1];
                                var bonus_item = BX.create('DIV', {
                                    props: {
                                        id: 'bonus_item_' + item.ID,
                                        className: 'bonus bonus_item_cart'
                                    }, html: _this.Params.CART.OFFER_LANG_BONUS + ' ' + item.BONUS
                                });
                                BX.remove(BX('bonus_item_' + item.ID));

                                if (parseFloat(item.BONUS) > 0)
                                    price_for_item.appendChild(bonus_item);
                            }
                        }
                        if (_this.Params.CARTBONUSPAY.ON == true) {
                            var bonuspay_block = BX.findChild(BX(_this.Params.CARTBONUSPAY.MAIN_BLOCK), {
                                'tag': 'div',
                                'class': _this.Params.CARTBONUSPAY.OUT_BLOCK
                            }, true);
                            var bonuspay = BX.create('DIV', {
                                props: {id: 'bonuspay'},
                                html: 'bonuspay for cart (test) !!!not use!!!'
                            });
                            bonuspay_block.appendChild(bonuspay);
                        }
                    },
                    onfailure: function () {

                    }
                });
            }
        },
        SOAAjax:function(){
            if(!BX.Sale || BX.Sale.OrderAjaxComponent == undefined)
                return;

            this.payBonusElement(BX.Sale.OrderAjaxComponent.result);

        },
        payBonusElement:function(result){
            if(result!=undefined) {
                BX.remove(BX('bonus_order'));
                BX.remove(BX('bonus_pay'));
                if (this.Params.SOA.MAIN_BLOCK) {
                    var total_block = BX.findChild(BX(this.Params.SOA.MAIN_BLOCK), {
                        'tag': 'div',
                        'class': this.Params.SOA.OUT_BLOCK
                    }, true, true);
                    if (total_block instanceof Array) {
                        var cnt = parseInt(total_block.length);

                        if (cnt > 2) {
                            cnt = cnt - 2;
                        }
                        if (cnt < 0) cnt = 0;
                    }

                    if (parseFloat(result.TOTAL.BONUS_ORDER) > 0) {
                        BX.remove(BX('bonus_order'));
                        var block_order_bonus = BX.create('DIV', {
                            attrs: {className: 'bx-soa-cart-total-line', id: 'bonus_order'},
                            html: '<div id="order_bonus"><span class="bx-soa-cart-t">' + this.Params.SOA.LANG_BONUS + '</span><span class="bx-soa-cart-d">' + result.TOTAL.BONUS_ORDER + '</span></div>'
                        });
                        if (total_block[cnt] && block_order_bonus)
                            BX.insertAfter(block_order_bonus, total_block[cnt]);
                    }
                    if (parseFloat(result.TOTAL.BONUS_PAY) > 0) {
                        BX.remove(BX('bonus_pay'));
                        var block_bonus_pay = BX.create('DIV', {
                            attrs: {className: 'bx-soa-cart-total-line', id: 'bonus_pay'},
                            html: '<div id="order_bonuspay"><span class="bx-soa-cart-t">' + this.Params.SOA.LANG_BONUSPAY + '</span><span class="bx-soa-cart-d">' + result.TOTAL.BONUS_PAY + '</span></div>'
                        });
                        if (total_block[cnt] && block_bonus_pay)
                            BX.insertAfter(block_bonus_pay, total_block[cnt]);
                    }

                }
                if (result.BONUSPAY && result.BONUSPAY.DISPLAY == true) {
                    var payment_block = BX.create('DIV', {
                        attrs: {
                            id: 'itrbonus-payment-bonus',
                            className: 'bx-soa-section'
                        }
                    });
                    var title = BX.create('DIV', {
                        attrs: {className: 'bx-soa-section-title-container'},
                        html: '<h2 class="bx-soa-section-title col-sm-9">' +
                            '<span class="bx-soa-section-title-count"></span>' + result.BONUSPAY.NAME + '</h2>'
                    });
                    var con = BX.create('DIV', {attrs: {className: 'bx-soa-section-content container-fluid'}});

                    var container = BX.create('DIV', {attrs: {className: 'bx-soa-pp row'}});
                    var container_in = BX.create('DIV', {attrs: {className: 'col-sm-9 bx-soa-pp-item-container'}});
                    var container_inpic = BX.create('DIV', {attrs: {className: 'col-sm-2 bx-soa-pp-item-container'}});
                    payment_block.appendChild(title);


                    var comment_block = BX.create('DIV', {
                        props: {className: 'bonus_comment'},
                        children: [
                            BX.create('strong', {text: this.Params.SOA.ITR_BONUS_TEXT_IN_YOU_ACCOUNT.replace("#SUMMA#", result.BONUSPAY.CURRENT_BONUS_BUDGET_FORMATED.replace("&nbsp;", " "))})
                        ]
                    });
                    var max_pay = BX.create('span', {html: '<br />' + this.Params.SOA.ITR_BONUS_TXT_YOU_MUST_PAY.replace("#PERCENT#", result.BONUSPAY.ORDER_PAY_PERCENT).replace("#MAXPAY#", result.BONUSPAY.MAXPAY_FORMATTED)});

                    var pay_field_block = BX.create('DIV', {
                        props: {id: 'bonus_payfield_block'},
                        children: [BX.create('strong', {text: result.BONUSPAY.USER_INPUT == true ? this.Params.SOA.ITR_BONUS_TXT_HOW_MUCH_BONUS : ''})]
                    });

                    var non_bonuspay = BX.create('DIV', {
                        props: {className: 'bonus_comment'},
                        children: [
                            BX.create('strong', {text: this.Params.SOA.ITR_BONUS_NONE_PAYBONUS})
                        ]
                    });

                    var input_field = BX.create('input', {
                        attrs: {
                            type: 'text',
                            id: 'BONUS_CNT',
                            value: result.BONUSPAY.MAXPAY,
                            name: 'BONUS_CNT',
                            disabled: result.PAY_BONUS_ACCOUNT ? 'disable' : '',
                        },
                        style: {
                            'width':'150px'
                        }
                    });
                    if(this.Params.CHECK_SMS.ON == 'Y') {
                        var sms_field_block = BX.create('DIV', {
                            props: {id: 'sms_payfield_block'},
                            children: [BX.create('strong', {text: this.Params.CHECK_SMS.SMSTEXT})]
                        });
                        var sms_code = BX.create('input', {
                            attrs: {
                                type: 'text',
                                id: 'SMSCODE',
                                value: result.SMSCODE ? result.SMSCODE : '',
                                name: 'SMSCODE',
                                disabled: result.CHECKSMS ? 'disable' : '',
                            },
                            style: {
                                'width': '150px'
                            }
                        });
                        var checksms = BX.create('LABEL', {
                                props: {className: 'bxr-subscribe-tab-link bxr-font-color bxr-border-color'},
                                children: [
                                    BX.create('INPUT', {
                                        attrs: {
                                            type: 'checkbox',
                                            id: 'CHECK_SMS',
                                            name: 'CHECK_SMS',
                                            value: 'Y',
                                            checked: result.CHECKSMS
                                        },
                                        style: {
                                            'display': 'none',
                                        },
                                        events: {
                                            click: BX.proxy(this.checksms, this)
                                        }
                                    }),
                                    BX.create('SPAN', {
                                        text: (result.CHECKSMS ? this.Params.SOA.ITR_BONUS_TXT_BONUSPAY_CANCEL : this.Params.SOA.ITR_BONUS_TXT_BONUSPAY_OK)
                                    })
                                ],
                                style: {
                                    'border': '1px solid #e3e6e8',
                                    'padding': '2px',
                                    'margin-left': '10px',
                                    'cursor': 'pointer',
                                },
                            }
                        );

                    }

                    var input_field_hidden = BX.create('input', {
                        attrs: {
                            type: 'hidden',
                            id: 'BONUS_CNT',
                            value: result.BONUSPAY.MAXPAY,
                            name: 'BONUS_CNT',
                        },
                    });

                    var hiddenpay = BX.create('INPUT', {
                        attrs: {
                            type: 'hidden',
                            name: 'PAY_BONUS_ACCOUNT',
                            value: 'N',
                        },

                    });

                    var label = BX.create('LABEL', {
                            props: {className: 'bxr-subscribe-tab-link bxr-font-color bxr-border-color'},
                            children: [
                                BX.create('INPUT', {
                                    attrs: {
                                        type: 'checkbox',
                                        id: 'PAY_BONUS_ACCOUNT',
                                        name: 'PAY_BONUS_ACCOUNT',
                                        value: 'Y',
                                        checked: result.PAY_BONUS_ACCOUNT
                                    },
                                    style: {
                                        'display': 'none',
                                    },
                                    events: {
                                        click: BX.proxy(this.selectBonusPaySystem, this)
                                    }
                                }),
                                BX.create('SPAN', {
                                    text: (result.PAY_BONUS_ACCOUNT ? this.Params.SOA.ITR_BONUS_TXT_BONUSPAY_CANCEL : this.Params.SOA.ITR_BONUS_TXT_BONUSPAY_OK)
                                })
                            ],
                            style: {
                                'border': '1px solid #e3e6e8',
                                'padding': '2px',
                                'margin-left': '10px',
                                'cursor': 'pointer',
                            },
                        }
                    );

                    var help = BX.create('DIV', {
                            props: {},
                            children: [
                                BX.create('A', {
                                    props: {href: BX.util.htmlspecialchars(this.Params.SOA.ITR_BONUS_PATH_TO_HELP)},
                                    html: BX.util.htmlspecialchars(this.Params.SOA.ITR_BONUS_TEXT_TO_HELP)
                                }),
                            ],
                        }
                    );

                    var logoNode = BX.create('DIV', {props: {className: 'bx-soa-pp-company-image'}});
                    var logotype = result.BONUSPAY.LOGOTIP_SRC;
                    if (logotype) {
                        logoNode.setAttribute('style',
                            'background-image: url(' + logotype + ');' +
                            'background-image: -webkit-image-set(url(' + logotype + ') 1x, url(' + logotype + ') 2x)'
                        );
                    }
                    var pic_con = BX.create('DIV', {attrs: {className: 'bx-soa-pp-company-graf-container'}});
                    pic_con.appendChild(logoNode);
                    container_inpic.appendChild(pic_con);
                    container_in.appendChild(comment_block);
                    if(result.BONUSPAY.MAXPAY > 0){
                        container_in.appendChild(max_pay);
                        container_in.appendChild(pay_field_block);
                        container_in.appendChild(hiddenpay);
                        container_in.appendChild(input_field_hidden);
                        if (result.BONUSPAY.USER_INPUT == true) {
                            container_in.appendChild(input_field);
                        }
                        container_in.appendChild(label);
                        if(this.Params.CHECK_SMS.ON == 'Y' && result.PAY_BONUS_ACCOUNT){
                            container_in.appendChild(sms_field_block);
                            container_in.appendChild(sms_code);
                            container_in.appendChild(checksms);
                        }
                    }else{
                        container_in.appendChild(non_bonuspay);
                    }

                    container_in.appendChild(help);

                    var p_block = BX.findChild(BX(this.Params.SOA.BONUSPAY_MAIN_BLOCK), {
                        'tag': 'div',
                        'class': this.Params.SOA.BONUSPAY_OUT_BLOCK
                    }, true);
                    BX.remove(BX('itrbonus-payment-bonus'));
                    container.appendChild(container_inpic);
                    container.appendChild(container_in);
                    con.appendChild(container);
                    payment_block.appendChild(con);
                    BX.insertAfter(payment_block, BX(this.Params.SOA.BONUSPAY_MAIN_BLOCK));
                } else {
                    BX.remove(BX('itrbonus-payment-bonus'));
                }
            }
        },
        checksms:function(){
            BX.Sale.OrderAjaxComponent.sendRequest();
        },
        selectBonusPaySystem:function(){
            BX.Sale.OrderAjaxComponent.sendRequest();
        },
        DetailElement:function (eventData){
            if(eventData) {
                this.elementID = eventData.newId;
                if (this.elementcount != 0)
                    this.ElementBonus();
            }
        },
        ChangeQuantity:function(eventData){
            this.elementID=eventData.newId;
            this.elementcount=eventData.Qua;
            this.ElementBonus();
        },
        ElementBonus:function(){
            if(this.Params.DETAIL.ON==true){
                var params= {
                    AJAX: 'Y',
                    SITE_ID:this.Params.SITE_ID,
                    PRODUCT_ID:this.elementID,
                    COUNTS :this.elementcount,
                    TEMPLATE:this.Params.DETAIL.COMP_TEMPLATE,
                };
                var _this=this;
                BX.ajax({
                    url: this.Params.DETAIL.PATH,
                    data: params,
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
                        var itog_block = BX.findChild(BX(_this.Params.DETAIL.MAIN_BLOCK), {'tag': 'div', 'class': _this.Params.DETAIL.OUT_BLOCK}, true);
                        if(_this.Params.DETAIL.COMP_TEMPLATE==''){
                            var bonus_all = BX.create('DIV', {props: {id: 'bonus_all'}, html: _this.Params.DETAIL.LANG_BONUS+' '+data });
                        }else{
                            var bonus_all = BX.create('DIV', {props: {id: 'bonus_all'}, html: data });
                        }
                        BX.remove(BX('bonus_all'));
                        //if(parseFloat(data) > 0) {
                        itog_block.appendChild(bonus_all);
                        //}
                    },
                    onfailure: function(){

                    }
                });
            }

        }

    };
})(window);