function VBCHSSProperty(arParams)
{
	if (!arParams)
	{
		return;
	}

	this.intERROR = 0;
	this.PREFIX = arParams.PREFIX;
	this.PREFIX_TR = this.PREFIX+'ROW_';
	this.FORM_ID = arParams.FORM_ID;
	this.TABLE_PROP_ID = arParams.TABLE_PROP_ID;
	this.PROP_COUNT_ID = arParams.PROP_COUNT_ID;
	this.TITLE = arParams.TITLE;
	this.CELLS = [];
	this.CELL_IND = -1;
	this.CELL_CENT = [];
	this.OBJNAME = arParams.OBJ;
	BX.ready(BX.delegate(this.Init,this));
}

VBCHSSProperty.prototype.Init = function()
{
	var clButtons = null,
		i = 0;

	this.FORM_DATA = BX(this.FORM_ID);
	if (!this.FORM_DATA)
	{
		this.intERROR = -1;
		return;
	}
	this.PROP_TBL = BX(this.TABLE_PROP_ID);
	if (!this.PROP_TBL)
	{
		this.intERROR = -1;
		return;
	}
	this.PROP_COUNT = BX(this.PROP_COUNT_ID);
	if (!this.PROP_COUNT)
	{
		this.intERROR = -1;
		return;
	}
	clButtons = BX.findChildren(this.PROP_TBL, {'tag': 'input','attribute': { 'type':'button'}}, true);
	if (!!clButtons)
	{
		for (i = 0; i < clButtons.length; i++)
		{
			BX.bind(clButtons[i], 'click', BX.proxy(this.ShowPropertyDialog, this));
		}
	}
	BX.addCustomEvent(this.FORM_DATA, 'onAutoSaveRestore', BX.delegate(this.onAutoSaveRestore, this));
};
VBCHSSProperty.prototype.GetPropInfo = function(ID)
{
	if (0 > this.intERROR)
	{
		return;
	}

	ID = this.PREFIX + ID;

	var arResult = {
		'TYPE' : this.FORM_DATA[ID+'_TYPE'].value,
		'NAME' : this.FORM_DATA[ID+'_NAME'].value,
		'MULTY' : (this.FORM_DATA[ID+'_MULTY_Y'].checked ? this.FORM_DATA[ID+'_MULTY_Y'].value : this.FORM_DATA[ID+'_MULTY_N'].value),
		'CODE' : this.FORM_DATA[ID+'_CODE'].value,
		'PROPINFO': this.FORM_DATA[ID+'_PROPINFO'].value
	};
	return arResult;
};

VBCHSSProperty.prototype.SetPropInfo = function(ID,arProp,formsess)
{
	var i = 0,
		PropMulti = null;
	if (0 > this.intERROR)
	{
		return;
	}
	if (!formsess)
	{
		return;
	}
	if (BX.bitrix_sessid() !== formsess)
	{
		return;
	}
	ID = this.PREFIX+ID;

	this.FORM_DATA[ID+'_NAME'].value = arProp.NAME;
	this.FORM_DATA[ID+'_CODE'].value = arProp.CODE;
	PropMulti = BX(ID+'_MULTY_Y');
	PropMulti.checked = ('Y' === arProp.MULTIPLE);
	this.FORM_DATA[ID+'_PROPINFO'].value = arProp.PROPINFO;
	for (i = 0; i < this.FORM_DATA[ID+'_TYPE'].length; i++)
	{
		if (arProp.PROPERTY_TYPE === this.FORM_DATA[ID+'_TYPE'].options[i].value)
		{
			this.FORM_DATA[ID+'_TYPE'].options[i].selected = true;
		}
	}
	BX.fireEvent(this.FORM_DATA[ID+'_NAME'], 'change');
};
VBCHSSProperty.prototype.GetProperty = function(strName)
{
	if (0 > this.intERROR)
	{
		return;
	}
	if (!strName || !this[strName])
	{
		return;
	}
	return this[strName];
};

VBCHSSProperty.prototype.SetProperty = function(strName,value)
{
	if (0 > this.intERROR)
	{
		return;
	}
	if (strName)
	{
		this[strName] = value;
	}
};
VBCHSSProperty.prototype.ShowPropertyDialog = function ()
{
	if (0 > this.intERROR)
	{
		return;
	}
	var target = BX.proxy_context,
		ID = '',
		arResult = {};

	if (!!target && target.hasAttribute('data-propid'))
	{
		ID = target.getAttribute('data-propid');

		arResult = {
			'PARAMS': {
				'PREFIX': this.PREFIX,
				'ID': ID,
				'TITLE': this.TITLE,
				'RECEIVER': this.OBJNAME
			},
			'PROP': this.GetPropInfo(ID),
			'sessid': BX.bitrix_sessid()
		};
		(new BX.CAdminDialog({
			'title': this.TITLE,
			'content_url': '/bitrix/admin/vbch_setting_edit.php?propedit='+ID+'&bxpublic=Y&receiver='+this.OBJNAME,
			'content_post': arResult,
			'draggable': true,
			'resizable': true,
			'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
		})).Show();
	}
};

VBCHSSProperty.prototype.SetCells = function(arCells,intIndex,arCenter)
{
	var i = 0;
	if (0 > this.intERROR)
	{
		return;
	}
	if (arCells)
	{
		this.CELLS = BX.clone(arCells,true);
	}
	for (i = 0; i < this.CELLS.length; i++)
	{
		this.CELLS[i] = this.CELLS[i].replace(/PREFIX/ig, this.PREFIX);
	}
	if (intIndex)
	{
		this.CELL_IND = intIndex;
	}
	if (arCenter)
	{
		this.CELL_CENT = BX.clone(arCenter,true);
	}
};
VBCHSSProperty.prototype.addPropRow = function()
{
	if (0 > this.intERROR)
	{
		console.log(this);
		return;
	}
	var i = 0,
		id = parseInt(this.PROP_COUNT.value, 10),
		needCell = '',
		newRow = null,
		oCell = null,
		typeHtml = '',
		clButtons = null;
	newRow = this.PROP_TBL.insertRow(this.PROP_TBL.rows.length);
	newRow.id = this.PREFIX_TR+'n'+id;
	for (i = 0; i < this.CELLS.length; i++)
	{
		oCell = newRow.insertCell(-1);
		typeHtml = this.CELLS[i];
		typeHtml = typeHtml.replace(/tmp_xxx/ig, 'n'+id);
		oCell.innerHTML = typeHtml;
	}
	for (i = 0; i < this.CELL_CENT.length; i++)
	{
		needCell = newRow.cells[this.CELL_CENT[i]-1];
		if (!!needCell)
		{
			BX.adjust(needCell, { style: {'textAlign': 'center', 'verticalAlign' : 'middle'} });
		}
	}
	needCell = newRow.cells[0];
	if (!!needCell)
	{
		BX.adjust(needCell, { style: {'verticalAlign' : 'middle'} });
	}
	if (newRow.cells[this.CELL_IND])
	{
		needCell = newRow.cells[this.CELL_IND];
		clButtons = BX.findChildren(needCell, {'tag': 'input','attribute': { 'type':'button'}}, true);
		if (!!clButtons)
		{
			for (i = 0; i < clButtons.length; i++)
			{
				BX.bind(clButtons[i], 'click', BX.proxy(this.ShowPropertyDialog, this));
			}
		}
	}
	BX.adminFormTools.modifyFormElements(this.FORM_ID);
	setTimeout(function() {
		var i = 0,
			l = 0,
			r = BX.findChildren(newRow.parentNode, {tag: /^(input|select|textarea)$/i}, true);
		if (r && r.length > 0)
		{
			for (i=0, l = r.length;i<l;i++)
			{
				if (r[i].form && r[i].form.BXAUTOSAVE)
				{
					r[i].form.BXAUTOSAVE.RegisterInput(r[i]);
				}
				else
				{
					break;
				}
			}
		}
	}, 10);
	this.PROP_COUNT.value = id + 1;
};
VBCHSSProperty.prototype.onAutoSaveRestore = function(ob, data)
{
	while (data['IB_PROPERTY_n' + this.PROP_COUNT.value + '_NAME'])
	{
		this.addPropRow();
	}
};

function addNewRow(tableID, row_to_clone)
{
	var tbl = document.getElementById(tableID);
	var cnt = tbl.rows.length;
	if(row_to_clone == null)
		row_to_clone = -2;
	var sHTML = tbl.rows[cnt+row_to_clone].cells[0].innerHTML;
	var oRow = tbl.insertRow(cnt+row_to_clone+1);
	var oCell = oRow.insertCell(0);

	var s, e, n, p;
	p = 0;
	while(true)
	{
		s = sHTML.indexOf('[n',p);
		if(s<0)break;
		e = sHTML.indexOf(']',s);
		if(e<0)break;
		n = parseInt(sHTML.substr(s+2,e-s));
		sHTML = sHTML.substr(0, s)+'[n'+(++n)+']'+sHTML.substr(e+1);
		p=s+1;
	}
	p = 0;
	while(true)
	{
		s = sHTML.indexOf('__n',p);
		if(s<0)break;
		e = sHTML.indexOf('_',s+2);
		if(e<0)break;
		n = parseInt(sHTML.substr(s+3,e-s));
		sHTML = sHTML.substr(0, s)+'__n'+(++n)+'_'+sHTML.substr(e+1);
		p=e+1;
	}
	p = 0;
	while(true)
	{
		s = sHTML.indexOf('__N',p);
		if(s<0)break;
		e = sHTML.indexOf('__',s+2);
		if(e<0)break;
		n = parseInt(sHTML.substr(s+3,e-s));
		sHTML = sHTML.substr(0, s)+'__N'+(++n)+'__'+sHTML.substr(e+2);
		p=e+2;
	}
	p = 0;
	while(true)
	{
		s = sHTML.indexOf('xxn',p);
		if(s<0)break;
		e = sHTML.indexOf('xx',s+2);
		if(e<0)break;
		n = parseInt(sHTML.substr(s+3,e-s));
		sHTML = sHTML.substr(0, s)+'xxn'+(++n)+'xx'+sHTML.substr(e+2);
		p=e+2;
	}
	p = 0;
	while(true)
	{
		s = sHTML.indexOf('%5Bn',p);
		if(s<0)break;
		e = sHTML.indexOf('%5D',s+3);
		if(e<0)break;
		n = parseInt(sHTML.substr(s+4,e-s));
		sHTML = sHTML.substr(0, s)+'%5Bn'+(++n)+'%5D'+sHTML.substr(e+3);
		p=e+3;
	}
	oCell.innerHTML = sHTML;

	var patt = new RegExp ("<"+"script"+">[^\000]*?<"+"\/"+"script"+">", "ig");
	var code = sHTML.match(patt);
	if(code)
	{
		for(var i = 0; i < code.length; i++)
		{
			if(code[i] != '')
			{
				s = code[i].substring(8, code[i].length-9);
				jsUtils.EvalGlobal(s);
			}
		}
	}
	if (BX && BX.adminPanel)
	{
		BX.adminPanel.modifyFormElements(oRow);
		BX.onCustomEvent('onAdminTabsChange');
	}
	setTimeout(function() {
		var r = BX.findChildren(oCell, {tag: /^(input|select|textarea)$/i});
		if (r && r.length > 0)
		{
			for (var i=0,l=r.length;i<l;i++)
			{
				if (r[i].form && r[i].form.BXAUTOSAVE)
					r[i].form.BXAUTOSAVE.RegisterInput(r[i]);
				else
					break;
			}
		}
	}, 10);
};