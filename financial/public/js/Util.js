function Util() {
	var self = this;
	var retryCount = 0;
	var xmlRequestAry = new Array();

	self.log = function (msg) {
		if (window.console) {
			console.log(msg);
		}
	}

	self.setHidden = function (el, oks) {
		if (el == null) return;

		if (oks) {
			el.style.display = "none";
		} else {
			el.style.display = "";
		}
	}

	self.getSpan = function (doc, Name) {
		var span = doc.getElementById(Name);
		if (span == null) return null;
		return span;
	}

	self.getSameNameSpan = function (template, SameName) {
		var list = new Array();
		if (template.children.length > 0) {
			var i = template.children.length;
			while (i-- > 0) {
				var tmp = template.children[i].id;
				if (tmp.indexOf(SameName) > -1) {
					list.push(template.children[i]);
				} else {
					var tmp = self.getSameNameSpanObj(template.children[i], SameName);
					list = list.concat(tmp);
				}
				count++;
			}
		}
		return list;
	}

	self.setClassName = function (div, classStr) {
		if (div.className) {
			div.className = classStr;
		} else {
			div.setAttribute("class", classStr);
		}
	}

	self.addPostPHP = function (eventName, url, parame, delegate) {
		var timer = null;
		var smstime = new Date().getTime();
		parame = "smstime=" + smstime + "&allms=" + smstime + "&" + parame;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (eventName == "betSubmit") {
					clearTimeout(timer);
					retryCount = 0;
				}
				// var emstime = new Date().getTime();
				// top.phpallms = emstime * 1 - smstime * 1;
				var phpData = self.strToObj(xmlhttp.responseText);
				if (delegate.phpDataCenter) delegate.phpDataCenter(eventName, phpData);
			} else {

			}
		};
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(parame);

		if (self.isMain(delegate) || eventName == "betSubmit") {
			if (xmlRequestAry[eventName] != null) self.cleanKeyXMLRequest(eventName);
			xmlRequestAry[eventName] = xmlhttp;
		}
		//下注遇時設定
		if (eventName == "betSubmit") {
			sendCode("301,addact,BET," + url + "|" + parame);
			var sec = (retryCount > 0) ? 3000 : 5000;
			if (retryCount > 3) {
				callFrame("betDelayView", "betDelay", "");
				clearTimeout(timer);
				self.cleanKeyXMLRequest(eventName);
				retryCount = 0;
				return;
			}
			timer = setTimeout(function () {
				retryCount++;
				clearTimeout(timer);
				self.cleanKeyXMLRequest(eventName);
				self.addPostPHP(eventName, url, parame, delegate); //timeout 遇時可以執行重送
			}, sec); //超過5秒遇時
		}
	}

	self.selectText = function (div) {
		div.focus();
		if (div.type == "text" || div.type == "password") div.setSelectionRange(0, div.size);
	}

	self.isMain = function (windows) {
		// Util   mainW   null 為了使 Util 可以在別的地方用  mainW 做判斷 是否null
		var mainW = document.getElementById("mainView");
		if (mainW == null) return false;
		if (mainW.contentWindow == windows) return true;
		else return false;
	}

	self.cleanXMLRequest = function () {
		for (var key in xmlRequestAry) {
			self.cleanKeyXMLRequest(key);
		}
		xmlRequestAry = new Array();
	}

	self.cleanKeyXMLRequest = function (key) {
		var xmlhttp = xmlRequestAry[key];
		xmlhttp.abort();
		xmlhttp.onreadystatechange = null;
		xmlhttp = null;
	}

	self.strToObj = function (str) {
		if (str.match("window.open")) {
			var scr = "<";
			scr += "script";
			scr += ">";
			str = str.replace(scr, "");
			str = str.replace(scr, "");
		}
		try {
			return (new Function("return " + str + ";"))();
		} catch (e) {
			return str;
		}
	}

	//
	self.moneyCheck = function (val) {
		var ret = "";
		if (val == 0) {
			return ret;
		}
		var patt1 = new RegExp(/[^0-9]/);
		var t = patt1.test(val);
		if (t) val = val.replace(/[^0-9]/, '');
		return val;
	}

	self.merge_object = function (obj1, obj2) {
		var obj3 = {};
		for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
		for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
		return obj3;
	}

	self.in_array = function (txt, ary) {
		for (var i = 0; i < ary.length; i++) {
			if (ary[i] == txt) return true;
		}
		return false;
	}
	//by key
	self.in_array_key = function (txt, ary) {
		for (var key in ary) {
			if (ary[key] == txt) return true;
		}
		return false;
	}

	self.array_indexOf = function (txt, ary, isMatch) {
		for (var i = 0; i < ary.length; i++) {
			if (isMatch) {
				if (ary[i] == txt) return i;
			} else {
				if (ary[i].indexOf(txt) != -1) return i;
			}
		}
		return -1;
	}

	self.addZero = function (val, b) {
		val = val.toString();
		if (val == "" || b == "") return val;
		val += "";
		if (b == 0) {
			var index = val.indexOf(".");
			if (index == -1) return val;
			return val.replace(".", "");
		}

		var str = "";
		var index = val.indexOf(".");

		if (index == -1) {
			val += ".";
			index = val.length - 1;
		}

		var r = b * 1 - (val.length - index - 1);
		for (i = 0; i < r; i++) {
			str += "0";
		}
		str = val + str;

		return str;
	}

	self.formatFloat = function (num, pos) {
		var size = Math.pow(10, pos);
		var ok = self.accDiv(Math.round(self.accMul(num, size)), size);
		var ret = self.addZero(ok, pos);

		return ret;
	}

	self.formatFloatF = function (num, pos) {
		var size = Math.pow(10, pos);
		var ok;
		if (num > 0) {
			ok = self.accDiv(Math.floor(self.accMul(num, size)), size);
		} else {
			ok = self.accDiv(Math.ceil(self.accMul(num, size)), size);
		}
		var ret = self.addZero(ok, pos);

		return ret;
	}

	//電投，電網投專用 轉為顯示數值
	self.showCustomized = function (arg) {
		var money = arg * 1;
		if (typeof money == "undefined") return arg;

		var forex = 1;
		// forex = 10000;	//元單位 數值轉換為 萬元單位
		var Q = 3;	//分位 quantile

		if (/\./.test(money)) {
			var sign = money < 0 ? -1 : 1;
			var regexp = new RegExp(/\.\d{1,}/);

			money = Math.abs(money);
			money += 0.000000001;  //  加一個極小數補足溢位
			money = money.toString().replace(regexp, "") * sign;
		}
		money = money / forex;

		var tmp = ("" + money).split(".");
		money = tmp[0];
		var pos = tmp[1];

		int_list = ("" + money).split("");
		money = "";
		for (var i = 0; i < int_list.length; i++) {
			if (i != 0 && !(i == 1 && int_list[0] == "-") && int_list[i] != "." && i % Q == int_list.length % Q) money += ",";
			money += int_list[i];
		}
		if (typeof pos != "undefined" && pos != "") money += "." + pos;

		return money;
	}

	//電投，電網投專用 轉為計算數值
	self.getCustomized = function (arg) {
		money = ("" + arg).replace(/\,/g, "") * 1;
		if (typeof money == "undefined") return arg;

		var forex = 1;
		// forex = 10000;	//萬元單位 數值轉換為 元單位

		if (/\./.test(money)) {
			var sign = money < 0 ? -1 : 1;
			var LOG = Math.log(forex) / Math.log(10);
			var regexp = new RegExp("(\\d+\.\\d{1," + LOG + "}).\\d+");

			money = Math.abs(money);
			money += 0.000000001;  //  加一個極小數補足溢位
			money = money.toString().replace(regexp, "$1").replace(/\./, "") * sign;
		} else {
			money = money * forex;
		}
		return money;
	}

	self.accAdd = function (arg1, arg2) {
		var r1, r2, m, c;
		try { r1 = arg1.toString().split(".")[1].length } catch (e) { r1 = 0 }
		try { r2 = arg2.toString().split(".")[1].length } catch (e) { r2 = 0 }
		c = Math.abs(r1 - r2);
		m = Math.pow(10, Math.max(r1, r2))
		if (c > 0) {
			var cm = Math.pow(10, c);
			if (r1 > r2) {
				arg1 = Number(arg1.toString().replace(".", ""));
				arg2 = Number(arg2.toString().replace(".", "")) * cm;
			} else {
				arg1 = Number(arg1.toString().replace(".", "")) * cm;
				arg2 = Number(arg2.toString().replace(".", ""));
			}
		} else {
			arg1 = Number(arg1.toString().replace(".", ""));
			arg2 = Number(arg2.toString().replace(".", ""));
		}
		return (arg1 + arg2) / m
	}

	self.accSub = function (arg1, arg2) {
		var r1, r2, m, n;
		try { r1 = arg1.toString().split(".")[1].length } catch (e) { r1 = 0 }
		try { r2 = arg2.toString().split(".")[1].length } catch (e) { r2 = 0 }
		m = Math.pow(10, Math.max(r1, r2));
		n = (r1 >= r2) ? r1 : r2;
		return ((arg1 * m - arg2 * m) / m).toFixed(n);
	}

	self.accMul = function (arg1, arg2) {
		var m = 0,
			s1 = arg1.toString(),
			s2 = arg2.toString();
		try { m += s1.split(".")[1].length } catch (e) { }
		try { m += s2.split(".")[1].length } catch (e) { }
		return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m)
	}

	self.accDiv = function (arg1, arg2) {
		var t1 = 0,
			t2 = 0,
			r1, r2;
		try { t1 = arg1.toString().split(".")[1].length } catch (e) { }
		try { t2 = arg2.toString().split(".")[1].length } catch (e) { }
		with (Math) {
			r1 = Number(arg1.toString().replace(".", ""))
			r2 = Number(arg2.toString().replace(".", ""))
			return (r1 / r2) * pow(10, t2 - t1);
		}
	}

	self.paserStr = function (d) {
		var date = d.getFullYear() + "-" + (((d.getMonth() + 1) < 10) ? "0" : "") + (d.getMonth() + 1) + "-" + ((d.getDate() < 10) ? "0" : "") + d.getDate();
		var time = ((d.getHours() < 10) ? "0" : "") + d.getHours() + ":" + ((d.getMinutes() < 10) ? "0" : "") + d.getMinutes() + ":" + ((d.getSeconds() < 10) ? "0" : "") + d.getSeconds();
		return date + " " + time;
	}

	// For todays date;
	Date.prototype.today = function () {
		return this.getFullYear() + "/" + (((this.getMonth() + 1) < 10) ? "0" : "") + (this.getMonth() + 1) + "/" + ((this.getDate() < 10) ? "0" : "") + this.getDate();
	}

	// For the time now
	Date.prototype.timeNow = function () {
		return ((this.getHours() < 10) ? "0" : "") + this.getHours() + ":" + ((this.getMinutes() < 10) ? "0" : "") + this.getMinutes() + ":" + ((this.getSeconds() < 10) ? "0" : "") + this.getSeconds();
	}

	//--------------------------------local storage--------------------------------
	/*
	 *get localStorage
	 */
	self.getLocalStorage = function () {

		var tmp_storage = null;

		try {
			tmp_storage = (window.localStorage) ? window.localStorage : window.globalStorage[strDomain];
			tmp_storage["init"] = "true";
		} catch (e) {
			//showErrorMsg(classname, "getLocalStorage", e.toString());
			return null;
		}

		return tmp_storage;
	}

	/*
	 *get localStorage item
	 */
	self.getLocalStorageItem = function (_key) {
		//if(!checkPrivate()) return "initFail";
		var tmp_storage = self.getLocalStorage();
		var tmp_value = null;

		if (tmp_storage) {
			if (tmp_storage[_key.toString()]) {
				tmp_value = tmp_storage[_key.toString()];
				return tmp_value;
			}
		}

		return null;

	}
	//
	self.doTime = function (strTime_now, strTime_close) {
		/* Chrome  IE8  ISO 8601
		var nowTime = new Date(strTime_now);
		var closeTime = new Date(strTime_close);
		var resultTime = closeTime - nowTime;
		*/
		var temp1 = strTime_now.split(" ");
		var temp2 = strTime_close.split(" ");
		var temp1time1 = temp1[0].split("-");
		var temp2time1 = temp2[0].split("-");
		var temp1time2 = temp1[1].split(":");
		var temp2time2 = temp2[1].split(":");
		var nowTime = new Date(temp1time1[0], temp1time1[1] - 1, temp1time1[2], temp1time2[0], temp1time2[1], temp1time2[2]);
		var closeTime = new Date(temp2time1[0], temp2time1[1] - 1, temp2time1[2], temp2time2[0], temp2time2[1], temp2time2[2]);
		var resultTime = closeTime - nowTime;

		nowTime = null;
		closeTime = null;
		temp1 = null;
		temp2 = null;
		temp1time1 = null;
		temp2time1 = null;
		temp1time2 = null;
		temp2time2 = null;
		return resultTime / 1000;
	}

	/*
	 *set localStorage item
	 */
	self.setLocalStorageItem = function (_key, _value) {
		//if(!checkPrivate()) return "initFail";
		var tmp_storage = self.getLocalStorage();
		if (tmp_storage) {
			try {
				tmp_storage[_key.toString()] = _value;
				return true;
			} catch (err) {
				//showErrorMsg(classname, "setLocalStorageItem", e.toString());
			}
		}

		return false;
	}

	/*
	 *remove localStorage item
	 */
	self.removeLocalStorageItem = function (_key) {
		// if (!checkPrivate()) return "initFail";
		var tmp_storage = self.getLocalStorage();
		if (tmp_storage) {
			if (tmp_storage[_key.toString()]) {
				try {
					tmp_storage.removeItem(_key.toString());
					return true;
				} catch (err) {
					//showErrorMsg(classname, "removeLocalStorageItem", e.toString());
				}
			}
		}

		return false;
	}

	//
	self.getObjCount = function (obj) {
		var out = 0;
		for (var key in obj) {
			out++;
		}

		return out;
	}

	//取得物件位置
	self.getObjAbsolute = function (obj) {
		var abs = new Object();

		abs["left"] = obj.offsetLeft;
		abs["top"] = obj.offsetTop;

		while (obj = obj.offsetParent) {
			abs["left"] += obj.offsetLeft;
			abs["top"] += obj.offsetTop;
		}

		return abs;
	}

	//僅手機端使用	防止Android 手機 下拉滑動會重新整理
	self.blockMovedownRefreash = function (wd, doc) {
		if (top.device == "I") return;
		wd.addEventListener('load', function () {

			var isWindowTop = false;
			var lastTouchY = 0;

			var touchStartHandler = function (e) {
				if (e.touches.length !== 1) return;
				lastTouchY = e.touches[0].clientY;
				isWindowTop = (wd.pageYOffset <= 50);
			};

			var touchMoveHandler = function (e) {
				var touchY = e.touches[0].clientY;
				var touchYmove = touchY - lastTouchY;
				lastTouchY = touchY;

				if (isWindowTop) {
					isWindowTop = false;
					if (touchYmove > 0) {
						e.preventDefault();
						return;
					}
				}

			};

			doc.addEventListener('touchstart', touchStartHandler, false);
			doc.addEventListener('touchmove', touchMoveHandler, { passive: false });

		});

	}

	//主domain
	self.getMainDomain = function (obj) {
		var domain = "";
		var host = "";
		if (obj != null) {
			if (typeof obj == "string") {
				obj = obj.replace("https://", "");
				obj = obj.replace("http://", "");
				obj = obj.split("?")[0];
				obj = obj.split("/")[0];
				host = obj;
			}
			if (obj === window) {
				host = obj.location.host;
			}
		}
		if (host == "") {
			host = window.location.host;
		}
		if (self.checkIsIPV4(host)) {
			return host;
		}
		var ary = host.split(".");
		var len = ary.length;
		domain = ary[len - 2] + "." + ary[len - 1];
		return domain
	}

	self.checkIsIPV4 = function (str) {
		var blocks = str.split(".");
		if (blocks.length === 4) {
			return blocks.every(function (block) {
				return parseInt(block, 10) >= 0 && parseInt(block, 10) <= 255;
			});
		}
		return false;
	}

	self.getObjAry = function (tmpScreen, aryStr, attribute, isOnly) {
		var newAry = new Array();
		var _attribute = attribute;

		if (tmpScreen != null & aryStr != null) {
			if (_attribute == null) _attribute = "id";
			newAry = self.getChildAry(tmpScreen.children, aryStr, newAry, _attribute, isOnly);
		}
		return newAry;
	}

	self.getChildAry = function (objAry, aryStr, newAry, attribute, isOnly) {
		for (var i = 0; i < objAry.length; i++) {
			var obj = objAry[i];
			var _id = obj.getAttribute(attribute);

			if (_id != null) {
				if (aryStr.indexOf("," + _id + ",") != -1) {
					if (attribute == "id") {
						newAry[_id] = obj;
					} else {
						newAry.push(obj);
					}
					if (isOnly) return newAry;
				}
			}

			if (obj.children.length > 0) self.getChildAry(obj.children, aryStr, newAry, attribute, isOnly);
		}
		return newAry;
	}

	self.mergeArray = function () {
		var newArray = new Object();

		for (i = 0; i < arguments.length; i++) {
			for (var key in arguments[i]) {
				newArray[key] = arguments[i][key];
			}
		}
		return newArray;
	}

	self.getObjLoop = function (divObj, tagID, isDFS) {
		if (divObj.id == tagID) return divObj;
		if (divObj.children.length > 0) {
			for (var i = 0; i < divObj.children.length; i++) {
				if (divObj.children[i].id == tagID) return divObj.children[i];
				if (isDFS) if (divObj.children[i].children.length > 0) {
					ret = self.getObjLoop(divObj.children[i], tagID);
					if (ret) return ret;
				}
			}
			if (!isDFS) {
				for (var i = 0; i < divObj.children.length; i++) {
					if (divObj.children[i].children.length > 0) {
						ret = self.getObjLoop(divObj.children[i], tagID);
						if (ret) return ret;
					}
				}
			}
		} else {
			if (divObj.id == tagID) return divObj;
		}
		return null;
	}

	//千分位
	self.formatThousand = function (num) {
		num = num + "";
		var re = /(-?\d+)(\d{3})/;
		while (re.test(num)) {
			num = num.replace(re, "$1,$2");
		}
		return num;
	}

	//編碼
	self.encode = function (Str) {
		// return Str;
		Str = encodeURIComponent(Str);
		var trans = "";
		var rev = "";
		var getcode = "";
		var i = 0;
		for (i = 0; i < Str.length; i++) {
			trans += (255 - Str.charCodeAt(i)).toString(16).toUpperCase();
		}
		i = 0;
		for (i = 0; i < trans.length; i++) {
			rev = trans.substr(i, 1) + rev;
		}
		getcode = rev.substr(5, rev.length - 5) + rev.substr(0, 5);
		return getcode;

	}

	//解碼
	self.decode = function (getstr) {
		// return getstr;
		var gettrans = "";
		var destr = "";
		var getrev = "";
		var i = 0;
		getrev = getstr.substr(getstr.length - 5, 5) + getstr.substr(0, getstr.length - 5);
		//trace("derev->"+getrev);
		for (i = 0; i < getrev.length; i++) {
			gettrans = getrev.substr(i, 1) + gettrans;
		}
		i = 0;
		for (i = 0; i < gettrans.length; i = i + 2) {
			destr += String.fromCharCode(255 - Number("0x" + gettrans.substr(i, 2)));
		}
		destr = decodeURIComponent(destr);
		return destr;
	}

	//切換按鈕 : document, 原按鈕, 變更按鈕, 原事件, 新事件, listenEvent, delegate, obj
	self.setRepeatButton = function (doc, btn1, btn2, event1, event2, listenEvt, delegate, obj) {
		if (btn1) self.setHidden(btn1, true);
		if (btn2) self.setHidden(btn2, false);

		if (event1 != "" && event1 != null) listenEvt.removeListener(event1, MouseEvent.CLICK);
		if (event2 != "" && event2 != null) listenEvt.addOnClick(event2, btn2, delegate, obj);
	}

	//切換顯示元件 : document, 新元件, 原元件
	self.chgRepeatSpan = function (doc, div1, div2) {
		var span1 = self.getSpan(doc, div1);
		var span2 = self.getSpan(doc, div2);

		self.setHidden(span1, false);
		self.setHidden(span2, true);
	}

	self.load_art = function (doc, artjson, langx) {
		if (langx == "") langx = langx || "zh-tw";
		var bod = doc.body.innerHTML;
		var json = artjson[langx];

		for (var key in json) bod = bod.replace(new RegExp('\\\*' + key + '\\\*', 'gi'), json[key]);
		doc.body.innerHTML = bod;
		doc.body.style.display = "";
	}

	self.checkScale = function (doc, limitHeight) {
		var dataObj = {};
		dataObj.dow = doc.body.children[0].clientWidth * 1;
		dataObj.doh = doc.body.children[0].clientHeight * 1;
		dataObj.bow = doc.body.clientWidth * 1;
		dataObj.boh = doc.body.clientHeight * 1;

		dataObj.wscale = dataObj.bow / dataObj.dow * 1;
		dataObj.hscale = dataObj.boh / dataObj.doh * 1;

		if (limitHeight) {
			dataObj.scale = (dataObj.wscale > dataObj.hscale) ? dataObj.hscale : dataObj.wscale;
		} else {
			dataObj.scale = dataObj.wscale;
		}
		if (dataObj.scale < 1) {
			dataObj.scale = 1;
			// doc.body.style.overflow = "hidden";
		} else {
			// doc.body.style.overflow = "";
		}

		dataObj.scale = Math.floor(dataObj.scale * 100) / 100;

		dataObj.top = dataObj.boh / 2 * (dataObj.scale - 1);
		dataObj.left = dataObj.bow / 2 * (dataObj.scale - 1);

		doc.body.style.transform = "scale(" + dataObj.scale + "," + dataObj.scale + ")";
		doc.body.style.position = "relative";
		doc.body.style.top = dataObj.top + "px";
		doc.body.style.left = "0px";
	}

	//檢查全螢幕／視窗模式 : 全螢幕回傳 true, 視窗模式回傳 false
	self.checkFullScreen = function () {
		try {
			if (top.document.fullscreenElement || top.document.msFullscreenElement || top.document.mozFullScreen || top.document.webkitIsFullScreen) {
				return true;
			} else {
				return false;
			}
		} catch (e) { }
	}

	//切換全螢幕/視窗模式
	self.setFullScreen = function (On, el) {
		try {
			if (On) {
				var el = el || top.document.documentElement;

				if (el.requestFullscreen) {
					el.requestFullscreen();
				} else if (el.msRequestFullscreen) {
					el.msRequestFullscreen();
				} else if (el.mozRequestFullScreen) {
					el.mozRequestFullScreen();
				} else if (el.webkitRequestFullscreen) {
					el.webkitRequestFullscreen();
				} else {
					this.log("Fullscreen API is not open supported");
				}
			} else {
				var el = top.document;

				if (el.cancelFullScreen) {
					el.cancelFullScreen();
				} else if (el.exitFullscreen) {
					el.exitFullscreen();
				} else if (el.msExitFullscreen) {
					el.msExitFullscreen();
				} else if (el.mozCancelFullScreen) {
					el.mozCancelFullScreen();
				} else if (el.webkitCancelFullScreen) {
					el.webkitCancelFullScreen();
				} else if (el.webkitExitFullscreen) {
					el.webkitExitFullscreen();
				} else {
					this.log("Fullscreen API is not close supported");
				}
			}
		} catch (e) { }
	}
}