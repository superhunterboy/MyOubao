//! moment.js
//! version : 2.13.0
//! authors : Tim Wood, Iskren Chernev, Moment.js contributors
//! license : MIT
//! momentjs.com
!function(a,b){"object"==typeof exports&&"undefined"!=typeof module?module.exports=b():"function"==typeof define&&define.amd?define(b):a.moment=b()}(this,function(){"use strict";function a(){return fd.apply(null,arguments)}function b(a){fd=a}function c(a){return a instanceof Array||"[object Array]"===Object.prototype.toString.call(a)}function d(a){return a instanceof Date||"[object Date]"===Object.prototype.toString.call(a)}function e(a,b){var c,d=[];for(c=0;c<a.length;++c)d.push(b(a[c],c));return d}function f(a,b){return Object.prototype.hasOwnProperty.call(a,b)}function g(a,b){for(var c in b)f(b,c)&&(a[c]=b[c]);return f(b,"toString")&&(a.toString=b.toString),f(b,"valueOf")&&(a.valueOf=b.valueOf),a}function h(a,b,c,d){return Ja(a,b,c,d,!0).utc()}function i(){return{empty:!1,unusedTokens:[],unusedInput:[],overflow:-2,charsLeftOver:0,nullInput:!1,invalidMonth:null,invalidFormat:!1,userInvalidated:!1,iso:!1,parsedDateParts:[],meridiem:null}}function j(a){return null==a._pf&&(a._pf=i()),a._pf}function k(a){if(null==a._isValid){var b=j(a),c=gd.call(b.parsedDateParts,function(a){return null!=a});a._isValid=!isNaN(a._d.getTime())&&b.overflow<0&&!b.empty&&!b.invalidMonth&&!b.invalidWeekday&&!b.nullInput&&!b.invalidFormat&&!b.userInvalidated&&(!b.meridiem||b.meridiem&&c),a._strict&&(a._isValid=a._isValid&&0===b.charsLeftOver&&0===b.unusedTokens.length&&void 0===b.bigHour)}return a._isValid}function l(a){var b=h(NaN);return null!=a?g(j(b),a):j(b).userInvalidated=!0,b}function m(a){return void 0===a}function n(a,b){var c,d,e;if(m(b._isAMomentObject)||(a._isAMomentObject=b._isAMomentObject),m(b._i)||(a._i=b._i),m(b._f)||(a._f=b._f),m(b._l)||(a._l=b._l),m(b._strict)||(a._strict=b._strict),m(b._tzm)||(a._tzm=b._tzm),m(b._isUTC)||(a._isUTC=b._isUTC),m(b._offset)||(a._offset=b._offset),m(b._pf)||(a._pf=j(b)),m(b._locale)||(a._locale=b._locale),hd.length>0)for(c in hd)d=hd[c],e=b[d],m(e)||(a[d]=e);return a}function o(b){n(this,b),this._d=new Date(null!=b._d?b._d.getTime():NaN),id===!1&&(id=!0,a.updateOffset(this),id=!1)}function p(a){return a instanceof o||null!=a&&null!=a._isAMomentObject}function q(a){return 0>a?Math.ceil(a):Math.floor(a)}function r(a){var b=+a,c=0;return 0!==b&&isFinite(b)&&(c=q(b)),c}function s(a,b,c){var d,e=Math.min(a.length,b.length),f=Math.abs(a.length-b.length),g=0;for(d=0;e>d;d++)(c&&a[d]!==b[d]||!c&&r(a[d])!==r(b[d]))&&g++;return g+f}function t(b){a.suppressDeprecationWarnings===!1&&"undefined"!=typeof console&&console.warn&&console.warn("Deprecation warning: "+b)}function u(b,c){var d=!0;return g(function(){return null!=a.deprecationHandler&&a.deprecationHandler(null,b),d&&(t(b+"\nArguments: "+Array.prototype.slice.call(arguments).join(", ")+"\n"+(new Error).stack),d=!1),c.apply(this,arguments)},c)}function v(b,c){null!=a.deprecationHandler&&a.deprecationHandler(b,c),jd[b]||(t(c),jd[b]=!0)}function w(a){return a instanceof Function||"[object Function]"===Object.prototype.toString.call(a)}function x(a){return"[object Object]"===Object.prototype.toString.call(a)}function y(a){var b,c;for(c in a)b=a[c],w(b)?this[c]=b:this["_"+c]=b;this._config=a,this._ordinalParseLenient=new RegExp(this._ordinalParse.source+"|"+/\d{1,2}/.source)}function z(a,b){var c,d=g({},a);for(c in b)f(b,c)&&(x(a[c])&&x(b[c])?(d[c]={},g(d[c],a[c]),g(d[c],b[c])):null!=b[c]?d[c]=b[c]:delete d[c]);return d}function A(a){null!=a&&this.set(a)}function B(a){return a?a.toLowerCase().replace("_","-"):a}function C(a){for(var b,c,d,e,f=0;f<a.length;){for(e=B(a[f]).split("-"),b=e.length,c=B(a[f+1]),c=c?c.split("-"):null;b>0;){if(d=D(e.slice(0,b).join("-")))return d;if(c&&c.length>=b&&s(e,c,!0)>=b-1)break;b--}f++}return null}function D(a){var b=null;if(!nd[a]&&"undefined"!=typeof module&&module&&module.exports)try{b=ld._abbr,require("./locale/"+a),E(b)}catch(c){}return nd[a]}function E(a,b){var c;return a&&(c=m(b)?H(a):F(a,b),c&&(ld=c)),ld._abbr}function F(a,b){return null!==b?(b.abbr=a,null!=nd[a]?(v("defineLocaleOverride","use moment.updateLocale(localeName, config) to change an existing locale. moment.defineLocale(localeName, config) should only be used for creating a new locale"),b=z(nd[a]._config,b)):null!=b.parentLocale&&(null!=nd[b.parentLocale]?b=z(nd[b.parentLocale]._config,b):v("parentLocaleUndefined","specified parentLocale is not defined yet")),nd[a]=new A(b),E(a),nd[a]):(delete nd[a],null)}function G(a,b){if(null!=b){var c;null!=nd[a]&&(b=z(nd[a]._config,b)),c=new A(b),c.parentLocale=nd[a],nd[a]=c,E(a)}else null!=nd[a]&&(null!=nd[a].parentLocale?nd[a]=nd[a].parentLocale:null!=nd[a]&&delete nd[a]);return nd[a]}function H(a){var b;if(a&&a._locale&&a._locale._abbr&&(a=a._locale._abbr),!a)return ld;if(!c(a)){if(b=D(a))return b;a=[a]}return C(a)}function I(){return kd(nd)}function J(a,b){var c=a.toLowerCase();od[c]=od[c+"s"]=od[b]=a}function K(a){return"string"==typeof a?od[a]||od[a.toLowerCase()]:void 0}function L(a){var b,c,d={};for(c in a)f(a,c)&&(b=K(c),b&&(d[b]=a[c]));return d}function M(b,c){return function(d){return null!=d?(O(this,b,d),a.updateOffset(this,c),this):N(this,b)}}function N(a,b){return a.isValid()?a._d["get"+(a._isUTC?"UTC":"")+b]():NaN}function O(a,b,c){a.isValid()&&a._d["set"+(a._isUTC?"UTC":"")+b](c)}function P(a,b){var c;if("object"==typeof a)for(c in a)this.set(c,a[c]);else if(a=K(a),w(this[a]))return this[a](b);return this}function Q(a,b,c){var d=""+Math.abs(a),e=b-d.length,f=a>=0;return(f?c?"+":"":"-")+Math.pow(10,Math.max(0,e)).toString().substr(1)+d}function R(a,b,c,d){var e=d;"string"==typeof d&&(e=function(){return this[d]()}),a&&(sd[a]=e),b&&(sd[b[0]]=function(){return Q(e.apply(this,arguments),b[1],b[2])}),c&&(sd[c]=function(){return this.localeData().ordinal(e.apply(this,arguments),a)})}function S(a){return a.match(/\[[\s\S]/)?a.replace(/^\[|\]$/g,""):a.replace(/\\/g,"")}function T(a){var b,c,d=a.match(pd);for(b=0,c=d.length;c>b;b++)sd[d[b]]?d[b]=sd[d[b]]:d[b]=S(d[b]);return function(b){var e,f="";for(e=0;c>e;e++)f+=d[e]instanceof Function?d[e].call(b,a):d[e];return f}}function U(a,b){return a.isValid()?(b=V(b,a.localeData()),rd[b]=rd[b]||T(b),rd[b](a)):a.localeData().invalidDate()}function V(a,b){function c(a){return b.longDateFormat(a)||a}var d=5;for(qd.lastIndex=0;d>=0&&qd.test(a);)a=a.replace(qd,c),qd.lastIndex=0,d-=1;return a}function W(a,b,c){Kd[a]=w(b)?b:function(a,d){return a&&c?c:b}}function X(a,b){return f(Kd,a)?Kd[a](b._strict,b._locale):new RegExp(Y(a))}function Y(a){return Z(a.replace("\\","").replace(/\\(\[)|\\(\])|\[([^\]\[]*)\]|\\(.)/g,function(a,b,c,d,e){return b||c||d||e}))}function Z(a){return a.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&")}function $(a,b){var c,d=b;for("string"==typeof a&&(a=[a]),"number"==typeof b&&(d=function(a,c){c[b]=r(a)}),c=0;c<a.length;c++)Ld[a[c]]=d}function _(a,b){$(a,function(a,c,d,e){d._w=d._w||{},b(a,d._w,d,e)})}function aa(a,b,c){null!=b&&f(Ld,a)&&Ld[a](b,c._a,c,a)}function ba(a,b){return new Date(Date.UTC(a,b+1,0)).getUTCDate()}function ca(a,b){return c(this._months)?this._months[a.month()]:this._months[Vd.test(b)?"format":"standalone"][a.month()]}function da(a,b){return c(this._monthsShort)?this._monthsShort[a.month()]:this._monthsShort[Vd.test(b)?"format":"standalone"][a.month()]}function ea(a,b,c){var d,e,f,g=a.toLocaleLowerCase();if(!this._monthsParse)for(this._monthsParse=[],this._longMonthsParse=[],this._shortMonthsParse=[],d=0;12>d;++d)f=h([2e3,d]),this._shortMonthsParse[d]=this.monthsShort(f,"").toLocaleLowerCase(),this._longMonthsParse[d]=this.months(f,"").toLocaleLowerCase();return c?"MMM"===b?(e=md.call(this._shortMonthsParse,g),-1!==e?e:null):(e=md.call(this._longMonthsParse,g),-1!==e?e:null):"MMM"===b?(e=md.call(this._shortMonthsParse,g),-1!==e?e:(e=md.call(this._longMonthsParse,g),-1!==e?e:null)):(e=md.call(this._longMonthsParse,g),-1!==e?e:(e=md.call(this._shortMonthsParse,g),-1!==e?e:null))}function fa(a,b,c){var d,e,f;if(this._monthsParseExact)return ea.call(this,a,b,c);for(this._monthsParse||(this._monthsParse=[],this._longMonthsParse=[],this._shortMonthsParse=[]),d=0;12>d;d++){if(e=h([2e3,d]),c&&!this._longMonthsParse[d]&&(this._longMonthsParse[d]=new RegExp("^"+this.months(e,"").replace(".","")+"$","i"),this._shortMonthsParse[d]=new RegExp("^"+this.monthsShort(e,"").replace(".","")+"$","i")),c||this._monthsParse[d]||(f="^"+this.months(e,"")+"|^"+this.monthsShort(e,""),this._monthsParse[d]=new RegExp(f.replace(".",""),"i")),c&&"MMMM"===b&&this._longMonthsParse[d].test(a))return d;if(c&&"MMM"===b&&this._shortMonthsParse[d].test(a))return d;if(!c&&this._monthsParse[d].test(a))return d}}function ga(a,b){var c;if(!a.isValid())return a;if("string"==typeof b)if(/^\d+$/.test(b))b=r(b);else if(b=a.localeData().monthsParse(b),"number"!=typeof b)return a;return c=Math.min(a.date(),ba(a.year(),b)),a._d["set"+(a._isUTC?"UTC":"")+"Month"](b,c),a}function ha(b){return null!=b?(ga(this,b),a.updateOffset(this,!0),this):N(this,"Month")}function ia(){return ba(this.year(),this.month())}function ja(a){return this._monthsParseExact?(f(this,"_monthsRegex")||la.call(this),a?this._monthsShortStrictRegex:this._monthsShortRegex):this._monthsShortStrictRegex&&a?this._monthsShortStrictRegex:this._monthsShortRegex}function ka(a){return this._monthsParseExact?(f(this,"_monthsRegex")||la.call(this),a?this._monthsStrictRegex:this._monthsRegex):this._monthsStrictRegex&&a?this._monthsStrictRegex:this._monthsRegex}function la(){function a(a,b){return b.length-a.length}var b,c,d=[],e=[],f=[];for(b=0;12>b;b++)c=h([2e3,b]),d.push(this.monthsShort(c,"")),e.push(this.months(c,"")),f.push(this.months(c,"")),f.push(this.monthsShort(c,""));for(d.sort(a),e.sort(a),f.sort(a),b=0;12>b;b++)d[b]=Z(d[b]),e[b]=Z(e[b]),f[b]=Z(f[b]);this._monthsRegex=new RegExp("^("+f.join("|")+")","i"),this._monthsShortRegex=this._monthsRegex,this._monthsStrictRegex=new RegExp("^("+e.join("|")+")","i"),this._monthsShortStrictRegex=new RegExp("^("+d.join("|")+")","i")}function ma(a){var b,c=a._a;return c&&-2===j(a).overflow&&(b=c[Nd]<0||c[Nd]>11?Nd:c[Od]<1||c[Od]>ba(c[Md],c[Nd])?Od:c[Pd]<0||c[Pd]>24||24===c[Pd]&&(0!==c[Qd]||0!==c[Rd]||0!==c[Sd])?Pd:c[Qd]<0||c[Qd]>59?Qd:c[Rd]<0||c[Rd]>59?Rd:c[Sd]<0||c[Sd]>999?Sd:-1,j(a)._overflowDayOfYear&&(Md>b||b>Od)&&(b=Od),j(a)._overflowWeeks&&-1===b&&(b=Td),j(a)._overflowWeekday&&-1===b&&(b=Ud),j(a).overflow=b),a}function na(a){var b,c,d,e,f,g,h=a._i,i=$d.exec(h)||_d.exec(h);if(i){for(j(a).iso=!0,b=0,c=be.length;c>b;b++)if(be[b][1].exec(i[1])){e=be[b][0],d=be[b][2]!==!1;break}if(null==e)return void(a._isValid=!1);if(i[3]){for(b=0,c=ce.length;c>b;b++)if(ce[b][1].exec(i[3])){f=(i[2]||" ")+ce[b][0];break}if(null==f)return void(a._isValid=!1)}if(!d&&null!=f)return void(a._isValid=!1);if(i[4]){if(!ae.exec(i[4]))return void(a._isValid=!1);g="Z"}a._f=e+(f||"")+(g||""),Ca(a)}else a._isValid=!1}function oa(b){var c=de.exec(b._i);return null!==c?void(b._d=new Date(+c[1])):(na(b),void(b._isValid===!1&&(delete b._isValid,a.createFromInputFallback(b))))}function pa(a,b,c,d,e,f,g){var h=new Date(a,b,c,d,e,f,g);return 100>a&&a>=0&&isFinite(h.getFullYear())&&h.setFullYear(a),h}function qa(a){var b=new Date(Date.UTC.apply(null,arguments));return 100>a&&a>=0&&isFinite(b.getUTCFullYear())&&b.setUTCFullYear(a),b}function ra(a){return sa(a)?366:365}function sa(a){return a%4===0&&a%100!==0||a%400===0}function ta(){return sa(this.year())}function ua(a,b,c){var d=7+b-c,e=(7+qa(a,0,d).getUTCDay()-b)%7;return-e+d-1}function va(a,b,c,d,e){var f,g,h=(7+c-d)%7,i=ua(a,d,e),j=1+7*(b-1)+h+i;return 0>=j?(f=a-1,g=ra(f)+j):j>ra(a)?(f=a+1,g=j-ra(a)):(f=a,g=j),{year:f,dayOfYear:g}}function wa(a,b,c){var d,e,f=ua(a.year(),b,c),g=Math.floor((a.dayOfYear()-f-1)/7)+1;return 1>g?(e=a.year()-1,d=g+xa(e,b,c)):g>xa(a.year(),b,c)?(d=g-xa(a.year(),b,c),e=a.year()+1):(e=a.year(),d=g),{week:d,year:e}}function xa(a,b,c){var d=ua(a,b,c),e=ua(a+1,b,c);return(ra(a)-d+e)/7}function ya(a,b,c){return null!=a?a:null!=b?b:c}function za(b){var c=new Date(a.now());return b._useUTC?[c.getUTCFullYear(),c.getUTCMonth(),c.getUTCDate()]:[c.getFullYear(),c.getMonth(),c.getDate()]}function Aa(a){var b,c,d,e,f=[];if(!a._d){for(d=za(a),a._w&&null==a._a[Od]&&null==a._a[Nd]&&Ba(a),a._dayOfYear&&(e=ya(a._a[Md],d[Md]),a._dayOfYear>ra(e)&&(j(a)._overflowDayOfYear=!0),c=qa(e,0,a._dayOfYear),a._a[Nd]=c.getUTCMonth(),a._a[Od]=c.getUTCDate()),b=0;3>b&&null==a._a[b];++b)a._a[b]=f[b]=d[b];for(;7>b;b++)a._a[b]=f[b]=null==a._a[b]?2===b?1:0:a._a[b];24===a._a[Pd]&&0===a._a[Qd]&&0===a._a[Rd]&&0===a._a[Sd]&&(a._nextDay=!0,a._a[Pd]=0),a._d=(a._useUTC?qa:pa).apply(null,f),null!=a._tzm&&a._d.setUTCMinutes(a._d.getUTCMinutes()-a._tzm),a._nextDay&&(a._a[Pd]=24)}}function Ba(a){var b,c,d,e,f,g,h,i;b=a._w,null!=b.GG||null!=b.W||null!=b.E?(f=1,g=4,c=ya(b.GG,a._a[Md],wa(Ka(),1,4).year),d=ya(b.W,1),e=ya(b.E,1),(1>e||e>7)&&(i=!0)):(f=a._locale._week.dow,g=a._locale._week.doy,c=ya(b.gg,a._a[Md],wa(Ka(),f,g).year),d=ya(b.w,1),null!=b.d?(e=b.d,(0>e||e>6)&&(i=!0)):null!=b.e?(e=b.e+f,(b.e<0||b.e>6)&&(i=!0)):e=f),1>d||d>xa(c,f,g)?j(a)._overflowWeeks=!0:null!=i?j(a)._overflowWeekday=!0:(h=va(c,d,e,f,g),a._a[Md]=h.year,a._dayOfYear=h.dayOfYear)}function Ca(b){if(b._f===a.ISO_8601)return void na(b);b._a=[],j(b).empty=!0;var c,d,e,f,g,h=""+b._i,i=h.length,k=0;for(e=V(b._f,b._locale).match(pd)||[],c=0;c<e.length;c++)f=e[c],d=(h.match(X(f,b))||[])[0],d&&(g=h.substr(0,h.indexOf(d)),g.length>0&&j(b).unusedInput.push(g),h=h.slice(h.indexOf(d)+d.length),k+=d.length),sd[f]?(d?j(b).empty=!1:j(b).unusedTokens.push(f),aa(f,d,b)):b._strict&&!d&&j(b).unusedTokens.push(f);j(b).charsLeftOver=i-k,h.length>0&&j(b).unusedInput.push(h),j(b).bigHour===!0&&b._a[Pd]<=12&&b._a[Pd]>0&&(j(b).bigHour=void 0),j(b).parsedDateParts=b._a.slice(0),j(b).meridiem=b._meridiem,b._a[Pd]=Da(b._locale,b._a[Pd],b._meridiem),Aa(b),ma(b)}function Da(a,b,c){var d;return null==c?b:null!=a.meridiemHour?a.meridiemHour(b,c):null!=a.isPM?(d=a.isPM(c),d&&12>b&&(b+=12),d||12!==b||(b=0),b):b}function Ea(a){var b,c,d,e,f;if(0===a._f.length)return j(a).invalidFormat=!0,void(a._d=new Date(NaN));for(e=0;e<a._f.length;e++)f=0,b=n({},a),null!=a._useUTC&&(b._useUTC=a._useUTC),b._f=a._f[e],Ca(b),k(b)&&(f+=j(b).charsLeftOver,f+=10*j(b).unusedTokens.length,j(b).score=f,(null==d||d>f)&&(d=f,c=b));g(a,c||b)}function Fa(a){if(!a._d){var b=L(a._i);a._a=e([b.year,b.month,b.day||b.date,b.hour,b.minute,b.second,b.millisecond],function(a){return a&&parseInt(a,10)}),Aa(a)}}function Ga(a){var b=new o(ma(Ha(a)));return b._nextDay&&(b.add(1,"d"),b._nextDay=void 0),b}function Ha(a){var b=a._i,e=a._f;return a._locale=a._locale||H(a._l),null===b||void 0===e&&""===b?l({nullInput:!0}):("string"==typeof b&&(a._i=b=a._locale.preparse(b)),p(b)?new o(ma(b)):(c(e)?Ea(a):e?Ca(a):d(b)?a._d=b:Ia(a),k(a)||(a._d=null),a))}function Ia(b){var f=b._i;void 0===f?b._d=new Date(a.now()):d(f)?b._d=new Date(f.valueOf()):"string"==typeof f?oa(b):c(f)?(b._a=e(f.slice(0),function(a){return parseInt(a,10)}),Aa(b)):"object"==typeof f?Fa(b):"number"==typeof f?b._d=new Date(f):a.createFromInputFallback(b)}function Ja(a,b,c,d,e){var f={};return"boolean"==typeof c&&(d=c,c=void 0),f._isAMomentObject=!0,f._useUTC=f._isUTC=e,f._l=c,f._i=a,f._f=b,f._strict=d,Ga(f)}function Ka(a,b,c,d){return Ja(a,b,c,d,!1)}function La(a,b){var d,e;if(1===b.length&&c(b[0])&&(b=b[0]),!b.length)return Ka();for(d=b[0],e=1;e<b.length;++e)(!b[e].isValid()||b[e][a](d))&&(d=b[e]);return d}function Ma(){var a=[].slice.call(arguments,0);return La("isBefore",a)}function Na(){var a=[].slice.call(arguments,0);return La("isAfter",a)}function Oa(a){var b=L(a),c=b.year||0,d=b.quarter||0,e=b.month||0,f=b.week||0,g=b.day||0,h=b.hour||0,i=b.minute||0,j=b.second||0,k=b.millisecond||0;this._milliseconds=+k+1e3*j+6e4*i+1e3*h*60*60,this._days=+g+7*f,this._months=+e+3*d+12*c,this._data={},this._locale=H(),this._bubble()}function Pa(a){return a instanceof Oa}function Qa(a,b){R(a,0,0,function(){var a=this.utcOffset(),c="+";return 0>a&&(a=-a,c="-"),c+Q(~~(a/60),2)+b+Q(~~a%60,2)})}function Ra(a,b){var c=(b||"").match(a)||[],d=c[c.length-1]||[],e=(d+"").match(ie)||["-",0,0],f=+(60*e[1])+r(e[2]);return"+"===e[0]?f:-f}function Sa(b,c){var e,f;return c._isUTC?(e=c.clone(),f=(p(b)||d(b)?b.valueOf():Ka(b).valueOf())-e.valueOf(),e._d.setTime(e._d.valueOf()+f),a.updateOffset(e,!1),e):Ka(b).local()}function Ta(a){return 15*-Math.round(a._d.getTimezoneOffset()/15)}function Ua(b,c){var d,e=this._offset||0;return this.isValid()?null!=b?("string"==typeof b?b=Ra(Hd,b):Math.abs(b)<16&&(b=60*b),!this._isUTC&&c&&(d=Ta(this)),this._offset=b,this._isUTC=!0,null!=d&&this.add(d,"m"),e!==b&&(!c||this._changeInProgress?jb(this,db(b-e,"m"),1,!1):this._changeInProgress||(this._changeInProgress=!0,a.updateOffset(this,!0),this._changeInProgress=null)),this):this._isUTC?e:Ta(this):null!=b?this:NaN}function Va(a,b){return null!=a?("string"!=typeof a&&(a=-a),this.utcOffset(a,b),this):-this.utcOffset()}function Wa(a){return this.utcOffset(0,a)}function Xa(a){return this._isUTC&&(this.utcOffset(0,a),this._isUTC=!1,a&&this.subtract(Ta(this),"m")),this}function Ya(){return this._tzm?this.utcOffset(this._tzm):"string"==typeof this._i&&this.utcOffset(Ra(Gd,this._i)),this}function Za(a){return this.isValid()?(a=a?Ka(a).utcOffset():0,(this.utcOffset()-a)%60===0):!1}function $a(){return this.utcOffset()>this.clone().month(0).utcOffset()||this.utcOffset()>this.clone().month(5).utcOffset()}function _a(){if(!m(this._isDSTShifted))return this._isDSTShifted;var a={};if(n(a,this),a=Ha(a),a._a){var b=a._isUTC?h(a._a):Ka(a._a);this._isDSTShifted=this.isValid()&&s(a._a,b.toArray())>0}else this._isDSTShifted=!1;return this._isDSTShifted}function ab(){return this.isValid()?!this._isUTC:!1}function bb(){return this.isValid()?this._isUTC:!1}function cb(){return this.isValid()?this._isUTC&&0===this._offset:!1}function db(a,b){var c,d,e,g=a,h=null;return Pa(a)?g={ms:a._milliseconds,d:a._days,M:a._months}:"number"==typeof a?(g={},b?g[b]=a:g.milliseconds=a):(h=je.exec(a))?(c="-"===h[1]?-1:1,g={y:0,d:r(h[Od])*c,h:r(h[Pd])*c,m:r(h[Qd])*c,s:r(h[Rd])*c,ms:r(h[Sd])*c}):(h=ke.exec(a))?(c="-"===h[1]?-1:1,g={y:eb(h[2],c),M:eb(h[3],c),w:eb(h[4],c),d:eb(h[5],c),h:eb(h[6],c),m:eb(h[7],c),s:eb(h[8],c)}):null==g?g={}:"object"==typeof g&&("from"in g||"to"in g)&&(e=gb(Ka(g.from),Ka(g.to)),g={},g.ms=e.milliseconds,g.M=e.months),d=new Oa(g),Pa(a)&&f(a,"_locale")&&(d._locale=a._locale),d}function eb(a,b){var c=a&&parseFloat(a.replace(",","."));return(isNaN(c)?0:c)*b}function fb(a,b){var c={milliseconds:0,months:0};return c.months=b.month()-a.month()+12*(b.year()-a.year()),a.clone().add(c.months,"M").isAfter(b)&&--c.months,c.milliseconds=+b-+a.clone().add(c.months,"M"),c}function gb(a,b){var c;return a.isValid()&&b.isValid()?(b=Sa(b,a),a.isBefore(b)?c=fb(a,b):(c=fb(b,a),c.milliseconds=-c.milliseconds,c.months=-c.months),c):{milliseconds:0,months:0}}function hb(a){return 0>a?-1*Math.round(-1*a):Math.round(a)}function ib(a,b){return function(c,d){var e,f;return null===d||isNaN(+d)||(v(b,"moment()."+b+"(period, number) is deprecated. Please use moment()."+b+"(number, period)."),f=c,c=d,d=f),c="string"==typeof c?+c:c,e=db(c,d),jb(this,e,a),this}}function jb(b,c,d,e){var f=c._milliseconds,g=hb(c._days),h=hb(c._months);b.isValid()&&(e=null==e?!0:e,f&&b._d.setTime(b._d.valueOf()+f*d),g&&O(b,"Date",N(b,"Date")+g*d),h&&ga(b,N(b,"Month")+h*d),e&&a.updateOffset(b,g||h))}function kb(a,b){var c=a||Ka(),d=Sa(c,this).startOf("day"),e=this.diff(d,"days",!0),f=-6>e?"sameElse":-1>e?"lastWeek":0>e?"lastDay":1>e?"sameDay":2>e?"nextDay":7>e?"nextWeek":"sameElse",g=b&&(w(b[f])?b[f]():b[f]);return this.format(g||this.localeData().calendar(f,this,Ka(c)))}function lb(){return new o(this)}function mb(a,b){var c=p(a)?a:Ka(a);return this.isValid()&&c.isValid()?(b=K(m(b)?"millisecond":b),"millisecond"===b?this.valueOf()>c.valueOf():c.valueOf()<this.clone().startOf(b).valueOf()):!1}function nb(a,b){var c=p(a)?a:Ka(a);return this.isValid()&&c.isValid()?(b=K(m(b)?"millisecond":b),"millisecond"===b?this.valueOf()<c.valueOf():this.clone().endOf(b).valueOf()<c.valueOf()):!1}function ob(a,b,c,d){return d=d||"()",("("===d[0]?this.isAfter(a,c):!this.isBefore(a,c))&&(")"===d[1]?this.isBefore(b,c):!this.isAfter(b,c))}function pb(a,b){var c,d=p(a)?a:Ka(a);return this.isValid()&&d.isValid()?(b=K(b||"millisecond"),"millisecond"===b?this.valueOf()===d.valueOf():(c=d.valueOf(),this.clone().startOf(b).valueOf()<=c&&c<=this.clone().endOf(b).valueOf())):!1}function qb(a,b){return this.isSame(a,b)||this.isAfter(a,b)}function rb(a,b){return this.isSame(a,b)||this.isBefore(a,b)}function sb(a,b,c){var d,e,f,g;return this.isValid()?(d=Sa(a,this),d.isValid()?(e=6e4*(d.utcOffset()-this.utcOffset()),b=K(b),"year"===b||"month"===b||"quarter"===b?(g=tb(this,d),"quarter"===b?g/=3:"year"===b&&(g/=12)):(f=this-d,g="second"===b?f/1e3:"minute"===b?f/6e4:"hour"===b?f/36e5:"day"===b?(f-e)/864e5:"week"===b?(f-e)/6048e5:f),c?g:q(g)):NaN):NaN}function tb(a,b){var c,d,e=12*(b.year()-a.year())+(b.month()-a.month()),f=a.clone().add(e,"months");return 0>b-f?(c=a.clone().add(e-1,"months"),d=(b-f)/(f-c)):(c=a.clone().add(e+1,"months"),d=(b-f)/(c-f)),-(e+d)||0}function ub(){return this.clone().locale("en").format("ddd MMM DD YYYY HH:mm:ss [GMT]ZZ")}function vb(){var a=this.clone().utc();return 0<a.year()&&a.year()<=9999?w(Date.prototype.toISOString)?this.toDate().toISOString():U(a,"YYYY-MM-DD[T]HH:mm:ss.SSS[Z]"):U(a,"YYYYYY-MM-DD[T]HH:mm:ss.SSS[Z]")}function wb(b){b||(b=this.isUtc()?a.defaultFormatUtc:a.defaultFormat);var c=U(this,b);return this.localeData().postformat(c)}function xb(a,b){return this.isValid()&&(p(a)&&a.isValid()||Ka(a).isValid())?db({to:this,from:a}).locale(this.locale()).humanize(!b):this.localeData().invalidDate()}function yb(a){return this.from(Ka(),a)}function zb(a,b){return this.isValid()&&(p(a)&&a.isValid()||Ka(a).isValid())?db({from:this,to:a}).locale(this.locale()).humanize(!b):this.localeData().invalidDate()}function Ab(a){return this.to(Ka(),a)}function Bb(a){var b;return void 0===a?this._locale._abbr:(b=H(a),null!=b&&(this._locale=b),this)}function Cb(){return this._locale}function Db(a){switch(a=K(a)){case"year":this.month(0);case"quarter":case"month":this.date(1);case"week":case"isoWeek":case"day":case"date":this.hours(0);case"hour":this.minutes(0);case"minute":this.seconds(0);case"second":this.milliseconds(0)}return"week"===a&&this.weekday(0),"isoWeek"===a&&this.isoWeekday(1),"quarter"===a&&this.month(3*Math.floor(this.month()/3)),this}function Eb(a){return a=K(a),void 0===a||"millisecond"===a?this:("date"===a&&(a="day"),this.startOf(a).add(1,"isoWeek"===a?"week":a).subtract(1,"ms"))}function Fb(){return this._d.valueOf()-6e4*(this._offset||0)}function Gb(){return Math.floor(this.valueOf()/1e3)}function Hb(){return this._offset?new Date(this.valueOf()):this._d}function Ib(){var a=this;return[a.year(),a.month(),a.date(),a.hour(),a.minute(),a.second(),a.millisecond()]}function Jb(){var a=this;return{years:a.year(),months:a.month(),date:a.date(),hours:a.hours(),minutes:a.minutes(),seconds:a.seconds(),milliseconds:a.milliseconds()}}function Kb(){return this.isValid()?this.toISOString():null}function Lb(){return k(this)}function Mb(){return g({},j(this))}function Nb(){return j(this).overflow}function Ob(){return{input:this._i,format:this._f,locale:this._locale,isUTC:this._isUTC,strict:this._strict}}function Pb(a,b){R(0,[a,a.length],0,b)}function Qb(a){return Ub.call(this,a,this.week(),this.weekday(),this.localeData()._week.dow,this.localeData()._week.doy)}function Rb(a){return Ub.call(this,a,this.isoWeek(),this.isoWeekday(),1,4)}function Sb(){return xa(this.year(),1,4)}function Tb(){var a=this.localeData()._week;return xa(this.year(),a.dow,a.doy)}function Ub(a,b,c,d,e){var f;return null==a?wa(this,d,e).year:(f=xa(a,d,e),b>f&&(b=f),Vb.call(this,a,b,c,d,e))}function Vb(a,b,c,d,e){var f=va(a,b,c,d,e),g=qa(f.year,0,f.dayOfYear);return this.year(g.getUTCFullYear()),this.month(g.getUTCMonth()),this.date(g.getUTCDate()),this}function Wb(a){return null==a?Math.ceil((this.month()+1)/3):this.month(3*(a-1)+this.month()%3)}function Xb(a){return wa(a,this._week.dow,this._week.doy).week}function Yb(){return this._week.dow}function Zb(){return this._week.doy}function $b(a){var b=this.localeData().week(this);return null==a?b:this.add(7*(a-b),"d")}function _b(a){var b=wa(this,1,4).week;return null==a?b:this.add(7*(a-b),"d")}function ac(a,b){return"string"!=typeof a?a:isNaN(a)?(a=b.weekdaysParse(a),"number"==typeof a?a:null):parseInt(a,10)}function bc(a,b){return c(this._weekdays)?this._weekdays[a.day()]:this._weekdays[this._weekdays.isFormat.test(b)?"format":"standalone"][a.day()]}function cc(a){return this._weekdaysShort[a.day()]}function dc(a){return this._weekdaysMin[a.day()]}function ec(a,b,c){var d,e,f,g=a.toLocaleLowerCase();if(!this._weekdaysParse)for(this._weekdaysParse=[],this._shortWeekdaysParse=[],this._minWeekdaysParse=[],d=0;7>d;++d)f=h([2e3,1]).day(d),this._minWeekdaysParse[d]=this.weekdaysMin(f,"").toLocaleLowerCase(),this._shortWeekdaysParse[d]=this.weekdaysShort(f,"").toLocaleLowerCase(),this._weekdaysParse[d]=this.weekdays(f,"").toLocaleLowerCase();return c?"dddd"===b?(e=md.call(this._weekdaysParse,g),-1!==e?e:null):"ddd"===b?(e=md.call(this._shortWeekdaysParse,g),-1!==e?e:null):(e=md.call(this._minWeekdaysParse,g),-1!==e?e:null):"dddd"===b?(e=md.call(this._weekdaysParse,g),-1!==e?e:(e=md.call(this._shortWeekdaysParse,g),-1!==e?e:(e=md.call(this._minWeekdaysParse,g),-1!==e?e:null))):"ddd"===b?(e=md.call(this._shortWeekdaysParse,g),-1!==e?e:(e=md.call(this._weekdaysParse,g),-1!==e?e:(e=md.call(this._minWeekdaysParse,g),-1!==e?e:null))):(e=md.call(this._minWeekdaysParse,g),-1!==e?e:(e=md.call(this._weekdaysParse,g),-1!==e?e:(e=md.call(this._shortWeekdaysParse,g),-1!==e?e:null)))}function fc(a,b,c){var d,e,f;if(this._weekdaysParseExact)return ec.call(this,a,b,c);for(this._weekdaysParse||(this._weekdaysParse=[],this._minWeekdaysParse=[],this._shortWeekdaysParse=[],this._fullWeekdaysParse=[]),d=0;7>d;d++){if(e=h([2e3,1]).day(d),c&&!this._fullWeekdaysParse[d]&&(this._fullWeekdaysParse[d]=new RegExp("^"+this.weekdays(e,"").replace(".",".?")+"$","i"),this._shortWeekdaysParse[d]=new RegExp("^"+this.weekdaysShort(e,"").replace(".",".?")+"$","i"),this._minWeekdaysParse[d]=new RegExp("^"+this.weekdaysMin(e,"").replace(".",".?")+"$","i")),this._weekdaysParse[d]||(f="^"+this.weekdays(e,"")+"|^"+this.weekdaysShort(e,"")+"|^"+this.weekdaysMin(e,""),this._weekdaysParse[d]=new RegExp(f.replace(".",""),"i")),c&&"dddd"===b&&this._fullWeekdaysParse[d].test(a))return d;if(c&&"ddd"===b&&this._shortWeekdaysParse[d].test(a))return d;if(c&&"dd"===b&&this._minWeekdaysParse[d].test(a))return d;if(!c&&this._weekdaysParse[d].test(a))return d}}function gc(a){if(!this.isValid())return null!=a?this:NaN;var b=this._isUTC?this._d.getUTCDay():this._d.getDay();return null!=a?(a=ac(a,this.localeData()),this.add(a-b,"d")):b}function hc(a){if(!this.isValid())return null!=a?this:NaN;var b=(this.day()+7-this.localeData()._week.dow)%7;return null==a?b:this.add(a-b,"d")}function ic(a){return this.isValid()?null==a?this.day()||7:this.day(this.day()%7?a:a-7):null!=a?this:NaN}function jc(a){return this._weekdaysParseExact?(f(this,"_weekdaysRegex")||mc.call(this),a?this._weekdaysStrictRegex:this._weekdaysRegex):this._weekdaysStrictRegex&&a?this._weekdaysStrictRegex:this._weekdaysRegex}function kc(a){return this._weekdaysParseExact?(f(this,"_weekdaysRegex")||mc.call(this),a?this._weekdaysShortStrictRegex:this._weekdaysShortRegex):this._weekdaysShortStrictRegex&&a?this._weekdaysShortStrictRegex:this._weekdaysShortRegex}function lc(a){return this._weekdaysParseExact?(f(this,"_weekdaysRegex")||mc.call(this),a?this._weekdaysMinStrictRegex:this._weekdaysMinRegex):this._weekdaysMinStrictRegex&&a?this._weekdaysMinStrictRegex:this._weekdaysMinRegex}function mc(){function a(a,b){return b.length-a.length}var b,c,d,e,f,g=[],i=[],j=[],k=[];for(b=0;7>b;b++)c=h([2e3,1]).day(b),d=this.weekdaysMin(c,""),e=this.weekdaysShort(c,""),f=this.weekdays(c,""),g.push(d),i.push(e),j.push(f),k.push(d),k.push(e),k.push(f);for(g.sort(a),i.sort(a),j.sort(a),k.sort(a),b=0;7>b;b++)i[b]=Z(i[b]),j[b]=Z(j[b]),k[b]=Z(k[b]);this._weekdaysRegex=new RegExp("^("+k.join("|")+")","i"),this._weekdaysShortRegex=this._weekdaysRegex,this._weekdaysMinRegex=this._weekdaysRegex,this._weekdaysStrictRegex=new RegExp("^("+j.join("|")+")","i"),this._weekdaysShortStrictRegex=new RegExp("^("+i.join("|")+")","i"),this._weekdaysMinStrictRegex=new RegExp("^("+g.join("|")+")","i")}function nc(a){var b=Math.round((this.clone().startOf("day")-this.clone().startOf("year"))/864e5)+1;return null==a?b:this.add(a-b,"d")}function oc(){return this.hours()%12||12}function pc(){return this.hours()||24}function qc(a,b){R(a,0,0,function(){return this.localeData().meridiem(this.hours(),this.minutes(),b)})}function rc(a,b){return b._meridiemParse}function sc(a){return"p"===(a+"").toLowerCase().charAt(0)}function tc(a,b,c){return a>11?c?"pm":"PM":c?"am":"AM"}function uc(a,b){b[Sd]=r(1e3*("0."+a))}function vc(){return this._isUTC?"UTC":""}function wc(){return this._isUTC?"Coordinated Universal Time":""}function xc(a){return Ka(1e3*a)}function yc(){return Ka.apply(null,arguments).parseZone()}function zc(a,b,c){var d=this._calendar[a];return w(d)?d.call(b,c):d}function Ac(a){var b=this._longDateFormat[a],c=this._longDateFormat[a.toUpperCase()];return b||!c?b:(this._longDateFormat[a]=c.replace(/MMMM|MM|DD|dddd/g,function(a){return a.slice(1)}),this._longDateFormat[a])}function Bc(){return this._invalidDate}function Cc(a){return this._ordinal.replace("%d",a)}function Dc(a){return a}function Ec(a,b,c,d){var e=this._relativeTime[c];return w(e)?e(a,b,c,d):e.replace(/%d/i,a)}function Fc(a,b){var c=this._relativeTime[a>0?"future":"past"];return w(c)?c(b):c.replace(/%s/i,b)}function Gc(a,b,c,d){var e=H(),f=h().set(d,b);return e[c](f,a)}function Hc(a,b,c){if("number"==typeof a&&(b=a,a=void 0),a=a||"",null!=b)return Gc(a,b,c,"month");var d,e=[];for(d=0;12>d;d++)e[d]=Gc(a,d,c,"month");return e}function Ic(a,b,c,d){"boolean"==typeof a?("number"==typeof b&&(c=b,b=void 0),b=b||""):(b=a,c=b,a=!1,"number"==typeof b&&(c=b,b=void 0),b=b||"");var e=H(),f=a?e._week.dow:0;if(null!=c)return Gc(b,(c+f)%7,d,"day");var g,h=[];for(g=0;7>g;g++)h[g]=Gc(b,(g+f)%7,d,"day");return h}function Jc(a,b){return Hc(a,b,"months")}function Kc(a,b){return Hc(a,b,"monthsShort")}function Lc(a,b,c){return Ic(a,b,c,"weekdays")}function Mc(a,b,c){return Ic(a,b,c,"weekdaysShort")}function Nc(a,b,c){return Ic(a,b,c,"weekdaysMin")}function Oc(){var a=this._data;return this._milliseconds=Le(this._milliseconds),this._days=Le(this._days),this._months=Le(this._months),a.milliseconds=Le(a.milliseconds),a.seconds=Le(a.seconds),a.minutes=Le(a.minutes),a.hours=Le(a.hours),a.months=Le(a.months),a.years=Le(a.years),this}function Pc(a,b,c,d){var e=db(b,c);return a._milliseconds+=d*e._milliseconds,a._days+=d*e._days,a._months+=d*e._months,a._bubble()}function Qc(a,b){return Pc(this,a,b,1)}function Rc(a,b){return Pc(this,a,b,-1)}function Sc(a){return 0>a?Math.floor(a):Math.ceil(a)}function Tc(){var a,b,c,d,e,f=this._milliseconds,g=this._days,h=this._months,i=this._data;return f>=0&&g>=0&&h>=0||0>=f&&0>=g&&0>=h||(f+=864e5*Sc(Vc(h)+g),g=0,h=0),i.milliseconds=f%1e3,a=q(f/1e3),i.seconds=a%60,b=q(a/60),i.minutes=b%60,c=q(b/60),i.hours=c%24,g+=q(c/24),e=q(Uc(g)),h+=e,g-=Sc(Vc(e)),d=q(h/12),h%=12,i.days=g,i.months=h,i.years=d,this}function Uc(a){return 4800*a/146097}function Vc(a){return 146097*a/4800}function Wc(a){var b,c,d=this._milliseconds;if(a=K(a),"month"===a||"year"===a)return b=this._days+d/864e5,c=this._months+Uc(b),"month"===a?c:c/12;switch(b=this._days+Math.round(Vc(this._months)),a){case"week":return b/7+d/6048e5;case"day":return b+d/864e5;case"hour":return 24*b+d/36e5;case"minute":return 1440*b+d/6e4;case"second":return 86400*b+d/1e3;case"millisecond":return Math.floor(864e5*b)+d;default:throw new Error("Unknown unit "+a)}}function Xc(){return this._milliseconds+864e5*this._days+this._months%12*2592e6+31536e6*r(this._months/12)}function Yc(a){return function(){return this.as(a)}}function Zc(a){
return a=K(a),this[a+"s"]()}function $c(a){return function(){return this._data[a]}}function _c(){return q(this.days()/7)}function ad(a,b,c,d,e){return e.relativeTime(b||1,!!c,a,d)}function bd(a,b,c){var d=db(a).abs(),e=_e(d.as("s")),f=_e(d.as("m")),g=_e(d.as("h")),h=_e(d.as("d")),i=_e(d.as("M")),j=_e(d.as("y")),k=e<af.s&&["s",e]||1>=f&&["m"]||f<af.m&&["mm",f]||1>=g&&["h"]||g<af.h&&["hh",g]||1>=h&&["d"]||h<af.d&&["dd",h]||1>=i&&["M"]||i<af.M&&["MM",i]||1>=j&&["y"]||["yy",j];return k[2]=b,k[3]=+a>0,k[4]=c,ad.apply(null,k)}function cd(a,b){return void 0===af[a]?!1:void 0===b?af[a]:(af[a]=b,!0)}function dd(a){var b=this.localeData(),c=bd(this,!a,b);return a&&(c=b.pastFuture(+this,c)),b.postformat(c)}function ed(){var a,b,c,d=bf(this._milliseconds)/1e3,e=bf(this._days),f=bf(this._months);a=q(d/60),b=q(a/60),d%=60,a%=60,c=q(f/12),f%=12;var g=c,h=f,i=e,j=b,k=a,l=d,m=this.asSeconds();return m?(0>m?"-":"")+"P"+(g?g+"Y":"")+(h?h+"M":"")+(i?i+"D":"")+(j||k||l?"T":"")+(j?j+"H":"")+(k?k+"M":"")+(l?l+"S":""):"P0D"}var fd,gd;gd=Array.prototype.some?Array.prototype.some:function(a){for(var b=Object(this),c=b.length>>>0,d=0;c>d;d++)if(d in b&&a.call(this,b[d],d,b))return!0;return!1};var hd=a.momentProperties=[],id=!1,jd={};a.suppressDeprecationWarnings=!1,a.deprecationHandler=null;var kd;kd=Object.keys?Object.keys:function(a){var b,c=[];for(b in a)f(a,b)&&c.push(b);return c};var ld,md,nd={},od={},pd=/(\[[^\[]*\])|(\\)?([Hh]mm(ss)?|Mo|MM?M?M?|Do|DDDo|DD?D?D?|ddd?d?|do?|w[o|w]?|W[o|W]?|Qo?|YYYYYY|YYYYY|YYYY|YY|gg(ggg?)?|GG(GGG?)?|e|E|a|A|hh?|HH?|kk?|mm?|ss?|S{1,9}|x|X|zz?|ZZ?|.)/g,qd=/(\[[^\[]*\])|(\\)?(LTS|LT|LL?L?L?|l{1,4})/g,rd={},sd={},td=/\d/,ud=/\d\d/,vd=/\d{3}/,wd=/\d{4}/,xd=/[+-]?\d{6}/,yd=/\d\d?/,zd=/\d\d\d\d?/,Ad=/\d\d\d\d\d\d?/,Bd=/\d{1,3}/,Cd=/\d{1,4}/,Dd=/[+-]?\d{1,6}/,Ed=/\d+/,Fd=/[+-]?\d+/,Gd=/Z|[+-]\d\d:?\d\d/gi,Hd=/Z|[+-]\d\d(?::?\d\d)?/gi,Id=/[+-]?\d+(\.\d{1,3})?/,Jd=/[0-9]*['a-z\u00A0-\u05FF\u0700-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+|[\u0600-\u06FF\/]+(\s*?[\u0600-\u06FF]+){1,2}/i,Kd={},Ld={},Md=0,Nd=1,Od=2,Pd=3,Qd=4,Rd=5,Sd=6,Td=7,Ud=8;md=Array.prototype.indexOf?Array.prototype.indexOf:function(a){var b;for(b=0;b<this.length;++b)if(this[b]===a)return b;return-1},R("M",["MM",2],"Mo",function(){return this.month()+1}),R("MMM",0,0,function(a){return this.localeData().monthsShort(this,a)}),R("MMMM",0,0,function(a){return this.localeData().months(this,a)}),J("month","M"),W("M",yd),W("MM",yd,ud),W("MMM",function(a,b){return b.monthsShortRegex(a)}),W("MMMM",function(a,b){return b.monthsRegex(a)}),$(["M","MM"],function(a,b){b[Nd]=r(a)-1}),$(["MMM","MMMM"],function(a,b,c,d){var e=c._locale.monthsParse(a,d,c._strict);null!=e?b[Nd]=e:j(c).invalidMonth=a});var Vd=/D[oD]?(\[[^\[\]]*\]|\s+)+MMMM?/,Wd="January_February_March_April_May_June_July_August_September_October_November_December".split("_"),Xd="Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_"),Yd=Jd,Zd=Jd,$d=/^\s*((?:[+-]\d{6}|\d{4})-(?:\d\d-\d\d|W\d\d-\d|W\d\d|\d\d\d|\d\d))(?:(T| )(\d\d(?::\d\d(?::\d\d(?:[.,]\d+)?)?)?)([\+\-]\d\d(?::?\d\d)?|\s*Z)?)?/,_d=/^\s*((?:[+-]\d{6}|\d{4})(?:\d\d\d\d|W\d\d\d|W\d\d|\d\d\d|\d\d))(?:(T| )(\d\d(?:\d\d(?:\d\d(?:[.,]\d+)?)?)?)([\+\-]\d\d(?::?\d\d)?|\s*Z)?)?/,ae=/Z|[+-]\d\d(?::?\d\d)?/,be=[["YYYYYY-MM-DD",/[+-]\d{6}-\d\d-\d\d/],["YYYY-MM-DD",/\d{4}-\d\d-\d\d/],["GGGG-[W]WW-E",/\d{4}-W\d\d-\d/],["GGGG-[W]WW",/\d{4}-W\d\d/,!1],["YYYY-DDD",/\d{4}-\d{3}/],["YYYY-MM",/\d{4}-\d\d/,!1],["YYYYYYMMDD",/[+-]\d{10}/],["YYYYMMDD",/\d{8}/],["GGGG[W]WWE",/\d{4}W\d{3}/],["GGGG[W]WW",/\d{4}W\d{2}/,!1],["YYYYDDD",/\d{7}/]],ce=[["HH:mm:ss.SSSS",/\d\d:\d\d:\d\d\.\d+/],["HH:mm:ss,SSSS",/\d\d:\d\d:\d\d,\d+/],["HH:mm:ss",/\d\d:\d\d:\d\d/],["HH:mm",/\d\d:\d\d/],["HHmmss.SSSS",/\d\d\d\d\d\d\.\d+/],["HHmmss,SSSS",/\d\d\d\d\d\d,\d+/],["HHmmss",/\d\d\d\d\d\d/],["HHmm",/\d\d\d\d/],["HH",/\d\d/]],de=/^\/?Date\((\-?\d+)/i;a.createFromInputFallback=u("moment construction falls back to js Date. This is discouraged and will be removed in upcoming major release. Please refer to https://github.com/moment/moment/issues/1407 for more info.",function(a){a._d=new Date(a._i+(a._useUTC?" UTC":""))}),R("Y",0,0,function(){var a=this.year();return 9999>=a?""+a:"+"+a}),R(0,["YY",2],0,function(){return this.year()%100}),R(0,["YYYY",4],0,"year"),R(0,["YYYYY",5],0,"year"),R(0,["YYYYYY",6,!0],0,"year"),J("year","y"),W("Y",Fd),W("YY",yd,ud),W("YYYY",Cd,wd),W("YYYYY",Dd,xd),W("YYYYYY",Dd,xd),$(["YYYYY","YYYYYY"],Md),$("YYYY",function(b,c){c[Md]=2===b.length?a.parseTwoDigitYear(b):r(b)}),$("YY",function(b,c){c[Md]=a.parseTwoDigitYear(b)}),$("Y",function(a,b){b[Md]=parseInt(a,10)}),a.parseTwoDigitYear=function(a){return r(a)+(r(a)>68?1900:2e3)};var ee=M("FullYear",!0);a.ISO_8601=function(){};var fe=u("moment().min is deprecated, use moment.max instead. https://github.com/moment/moment/issues/1548",function(){var a=Ka.apply(null,arguments);return this.isValid()&&a.isValid()?this>a?this:a:l()}),ge=u("moment().max is deprecated, use moment.min instead. https://github.com/moment/moment/issues/1548",function(){var a=Ka.apply(null,arguments);return this.isValid()&&a.isValid()?a>this?this:a:l()}),he=function(){return Date.now?Date.now():+new Date};Qa("Z",":"),Qa("ZZ",""),W("Z",Hd),W("ZZ",Hd),$(["Z","ZZ"],function(a,b,c){c._useUTC=!0,c._tzm=Ra(Hd,a)});var ie=/([\+\-]|\d\d)/gi;a.updateOffset=function(){};var je=/^(\-)?(?:(\d*)[. ])?(\d+)\:(\d+)(?:\:(\d+)\.?(\d{3})?\d*)?$/,ke=/^(-)?P(?:(-?[0-9,.]*)Y)?(?:(-?[0-9,.]*)M)?(?:(-?[0-9,.]*)W)?(?:(-?[0-9,.]*)D)?(?:T(?:(-?[0-9,.]*)H)?(?:(-?[0-9,.]*)M)?(?:(-?[0-9,.]*)S)?)?$/;db.fn=Oa.prototype;var le=ib(1,"add"),me=ib(-1,"subtract");a.defaultFormat="YYYY-MM-DDTHH:mm:ssZ",a.defaultFormatUtc="YYYY-MM-DDTHH:mm:ss[Z]";var ne=u("moment().lang() is deprecated. Instead, use moment().localeData() to get the language configuration. Use moment().locale() to change languages.",function(a){return void 0===a?this.localeData():this.locale(a)});R(0,["gg",2],0,function(){return this.weekYear()%100}),R(0,["GG",2],0,function(){return this.isoWeekYear()%100}),Pb("gggg","weekYear"),Pb("ggggg","weekYear"),Pb("GGGG","isoWeekYear"),Pb("GGGGG","isoWeekYear"),J("weekYear","gg"),J("isoWeekYear","GG"),W("G",Fd),W("g",Fd),W("GG",yd,ud),W("gg",yd,ud),W("GGGG",Cd,wd),W("gggg",Cd,wd),W("GGGGG",Dd,xd),W("ggggg",Dd,xd),_(["gggg","ggggg","GGGG","GGGGG"],function(a,b,c,d){b[d.substr(0,2)]=r(a)}),_(["gg","GG"],function(b,c,d,e){c[e]=a.parseTwoDigitYear(b)}),R("Q",0,"Qo","quarter"),J("quarter","Q"),W("Q",td),$("Q",function(a,b){b[Nd]=3*(r(a)-1)}),R("w",["ww",2],"wo","week"),R("W",["WW",2],"Wo","isoWeek"),J("week","w"),J("isoWeek","W"),W("w",yd),W("ww",yd,ud),W("W",yd),W("WW",yd,ud),_(["w","ww","W","WW"],function(a,b,c,d){b[d.substr(0,1)]=r(a)});var oe={dow:0,doy:6};R("D",["DD",2],"Do","date"),J("date","D"),W("D",yd),W("DD",yd,ud),W("Do",function(a,b){return a?b._ordinalParse:b._ordinalParseLenient}),$(["D","DD"],Od),$("Do",function(a,b){b[Od]=r(a.match(yd)[0],10)});var pe=M("Date",!0);R("d",0,"do","day"),R("dd",0,0,function(a){return this.localeData().weekdaysMin(this,a)}),R("ddd",0,0,function(a){return this.localeData().weekdaysShort(this,a)}),R("dddd",0,0,function(a){return this.localeData().weekdays(this,a)}),R("e",0,0,"weekday"),R("E",0,0,"isoWeekday"),J("day","d"),J("weekday","e"),J("isoWeekday","E"),W("d",yd),W("e",yd),W("E",yd),W("dd",function(a,b){return b.weekdaysMinRegex(a)}),W("ddd",function(a,b){return b.weekdaysShortRegex(a)}),W("dddd",function(a,b){return b.weekdaysRegex(a)}),_(["dd","ddd","dddd"],function(a,b,c,d){var e=c._locale.weekdaysParse(a,d,c._strict);null!=e?b.d=e:j(c).invalidWeekday=a}),_(["d","e","E"],function(a,b,c,d){b[d]=r(a)});var qe="Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),re="Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_"),se="Su_Mo_Tu_We_Th_Fr_Sa".split("_"),te=Jd,ue=Jd,ve=Jd;R("DDD",["DDDD",3],"DDDo","dayOfYear"),J("dayOfYear","DDD"),W("DDD",Bd),W("DDDD",vd),$(["DDD","DDDD"],function(a,b,c){c._dayOfYear=r(a)}),R("H",["HH",2],0,"hour"),R("h",["hh",2],0,oc),R("k",["kk",2],0,pc),R("hmm",0,0,function(){return""+oc.apply(this)+Q(this.minutes(),2)}),R("hmmss",0,0,function(){return""+oc.apply(this)+Q(this.minutes(),2)+Q(this.seconds(),2)}),R("Hmm",0,0,function(){return""+this.hours()+Q(this.minutes(),2)}),R("Hmmss",0,0,function(){return""+this.hours()+Q(this.minutes(),2)+Q(this.seconds(),2)}),qc("a",!0),qc("A",!1),J("hour","h"),W("a",rc),W("A",rc),W("H",yd),W("h",yd),W("HH",yd,ud),W("hh",yd,ud),W("hmm",zd),W("hmmss",Ad),W("Hmm",zd),W("Hmmss",Ad),$(["H","HH"],Pd),$(["a","A"],function(a,b,c){c._isPm=c._locale.isPM(a),c._meridiem=a}),$(["h","hh"],function(a,b,c){b[Pd]=r(a),j(c).bigHour=!0}),$("hmm",function(a,b,c){var d=a.length-2;b[Pd]=r(a.substr(0,d)),b[Qd]=r(a.substr(d)),j(c).bigHour=!0}),$("hmmss",function(a,b,c){var d=a.length-4,e=a.length-2;b[Pd]=r(a.substr(0,d)),b[Qd]=r(a.substr(d,2)),b[Rd]=r(a.substr(e)),j(c).bigHour=!0}),$("Hmm",function(a,b,c){var d=a.length-2;b[Pd]=r(a.substr(0,d)),b[Qd]=r(a.substr(d))}),$("Hmmss",function(a,b,c){var d=a.length-4,e=a.length-2;b[Pd]=r(a.substr(0,d)),b[Qd]=r(a.substr(d,2)),b[Rd]=r(a.substr(e))});var we=/[ap]\.?m?\.?/i,xe=M("Hours",!0);R("m",["mm",2],0,"minute"),J("minute","m"),W("m",yd),W("mm",yd,ud),$(["m","mm"],Qd);var ye=M("Minutes",!1);R("s",["ss",2],0,"second"),J("second","s"),W("s",yd),W("ss",yd,ud),$(["s","ss"],Rd);var ze=M("Seconds",!1);R("S",0,0,function(){return~~(this.millisecond()/100)}),R(0,["SS",2],0,function(){return~~(this.millisecond()/10)}),R(0,["SSS",3],0,"millisecond"),R(0,["SSSS",4],0,function(){return 10*this.millisecond()}),R(0,["SSSSS",5],0,function(){return 100*this.millisecond()}),R(0,["SSSSSS",6],0,function(){return 1e3*this.millisecond()}),R(0,["SSSSSSS",7],0,function(){return 1e4*this.millisecond()}),R(0,["SSSSSSSS",8],0,function(){return 1e5*this.millisecond()}),R(0,["SSSSSSSSS",9],0,function(){return 1e6*this.millisecond()}),J("millisecond","ms"),W("S",Bd,td),W("SS",Bd,ud),W("SSS",Bd,vd);var Ae;for(Ae="SSSS";Ae.length<=9;Ae+="S")W(Ae,Ed);for(Ae="S";Ae.length<=9;Ae+="S")$(Ae,uc);var Be=M("Milliseconds",!1);R("z",0,0,"zoneAbbr"),R("zz",0,0,"zoneName");var Ce=o.prototype;Ce.add=le,Ce.calendar=kb,Ce.clone=lb,Ce.diff=sb,Ce.endOf=Eb,Ce.format=wb,Ce.from=xb,Ce.fromNow=yb,Ce.to=zb,Ce.toNow=Ab,Ce.get=P,Ce.invalidAt=Nb,Ce.isAfter=mb,Ce.isBefore=nb,Ce.isBetween=ob,Ce.isSame=pb,Ce.isSameOrAfter=qb,Ce.isSameOrBefore=rb,Ce.isValid=Lb,Ce.lang=ne,Ce.locale=Bb,Ce.localeData=Cb,Ce.max=ge,Ce.min=fe,Ce.parsingFlags=Mb,Ce.set=P,Ce.startOf=Db,Ce.subtract=me,Ce.toArray=Ib,Ce.toObject=Jb,Ce.toDate=Hb,Ce.toISOString=vb,Ce.toJSON=Kb,Ce.toString=ub,Ce.unix=Gb,Ce.valueOf=Fb,Ce.creationData=Ob,Ce.year=ee,Ce.isLeapYear=ta,Ce.weekYear=Qb,Ce.isoWeekYear=Rb,Ce.quarter=Ce.quarters=Wb,Ce.month=ha,Ce.daysInMonth=ia,Ce.week=Ce.weeks=$b,Ce.isoWeek=Ce.isoWeeks=_b,Ce.weeksInYear=Tb,Ce.isoWeeksInYear=Sb,Ce.date=pe,Ce.day=Ce.days=gc,Ce.weekday=hc,Ce.isoWeekday=ic,Ce.dayOfYear=nc,Ce.hour=Ce.hours=xe,Ce.minute=Ce.minutes=ye,Ce.second=Ce.seconds=ze,Ce.millisecond=Ce.milliseconds=Be,Ce.utcOffset=Ua,Ce.utc=Wa,Ce.local=Xa,Ce.parseZone=Ya,Ce.hasAlignedHourOffset=Za,Ce.isDST=$a,Ce.isDSTShifted=_a,Ce.isLocal=ab,Ce.isUtcOffset=bb,Ce.isUtc=cb,Ce.isUTC=cb,Ce.zoneAbbr=vc,Ce.zoneName=wc,Ce.dates=u("dates accessor is deprecated. Use date instead.",pe),Ce.months=u("months accessor is deprecated. Use month instead",ha),Ce.years=u("years accessor is deprecated. Use year instead",ee),Ce.zone=u("moment().zone is deprecated, use moment().utcOffset instead. https://github.com/moment/moment/issues/1779",Va);var De=Ce,Ee={sameDay:"[Today at] LT",nextDay:"[Tomorrow at] LT",nextWeek:"dddd [at] LT",lastDay:"[Yesterday at] LT",lastWeek:"[Last] dddd [at] LT",sameElse:"L"},Fe={LTS:"h:mm:ss A",LT:"h:mm A",L:"MM/DD/YYYY",LL:"MMMM D, YYYY",LLL:"MMMM D, YYYY h:mm A",LLLL:"dddd, MMMM D, YYYY h:mm A"},Ge="Invalid date",He="%d",Ie=/\d{1,2}/,Je={future:"in %s",past:"%s ago",s:"a few seconds",m:"a minute",mm:"%d minutes",h:"an hour",hh:"%d hours",d:"a day",dd:"%d days",M:"a month",MM:"%d months",y:"a year",yy:"%d years"},Ke=A.prototype;Ke._calendar=Ee,Ke.calendar=zc,Ke._longDateFormat=Fe,Ke.longDateFormat=Ac,Ke._invalidDate=Ge,Ke.invalidDate=Bc,Ke._ordinal=He,Ke.ordinal=Cc,Ke._ordinalParse=Ie,Ke.preparse=Dc,Ke.postformat=Dc,Ke._relativeTime=Je,Ke.relativeTime=Ec,Ke.pastFuture=Fc,Ke.set=y,Ke.months=ca,Ke._months=Wd,Ke.monthsShort=da,Ke._monthsShort=Xd,Ke.monthsParse=fa,Ke._monthsRegex=Zd,Ke.monthsRegex=ka,Ke._monthsShortRegex=Yd,Ke.monthsShortRegex=ja,Ke.week=Xb,Ke._week=oe,Ke.firstDayOfYear=Zb,Ke.firstDayOfWeek=Yb,Ke.weekdays=bc,Ke._weekdays=qe,Ke.weekdaysMin=dc,Ke._weekdaysMin=se,Ke.weekdaysShort=cc,Ke._weekdaysShort=re,Ke.weekdaysParse=fc,Ke._weekdaysRegex=te,Ke.weekdaysRegex=jc,Ke._weekdaysShortRegex=ue,Ke.weekdaysShortRegex=kc,Ke._weekdaysMinRegex=ve,Ke.weekdaysMinRegex=lc,Ke.isPM=sc,Ke._meridiemParse=we,Ke.meridiem=tc,E("en",{ordinalParse:/\d{1,2}(th|st|nd|rd)/,ordinal:function(a){var b=a%10,c=1===r(a%100/10)?"th":1===b?"st":2===b?"nd":3===b?"rd":"th";return a+c}}),a.lang=u("moment.lang is deprecated. Use moment.locale instead.",E),a.langData=u("moment.langData is deprecated. Use moment.localeData instead.",H);var Le=Math.abs,Me=Yc("ms"),Ne=Yc("s"),Oe=Yc("m"),Pe=Yc("h"),Qe=Yc("d"),Re=Yc("w"),Se=Yc("M"),Te=Yc("y"),Ue=$c("milliseconds"),Ve=$c("seconds"),We=$c("minutes"),Xe=$c("hours"),Ye=$c("days"),Ze=$c("months"),$e=$c("years"),_e=Math.round,af={s:45,m:45,h:22,d:26,M:11},bf=Math.abs,cf=Oa.prototype;cf.abs=Oc,cf.add=Qc,cf.subtract=Rc,cf.as=Wc,cf.asMilliseconds=Me,cf.asSeconds=Ne,cf.asMinutes=Oe,cf.asHours=Pe,cf.asDays=Qe,cf.asWeeks=Re,cf.asMonths=Se,cf.asYears=Te,cf.valueOf=Xc,cf._bubble=Tc,cf.get=Zc,cf.milliseconds=Ue,cf.seconds=Ve,cf.minutes=We,cf.hours=Xe,cf.days=Ye,cf.weeks=_c,cf.months=Ze,cf.years=$e,cf.humanize=dd,cf.toISOString=ed,cf.toString=ed,cf.toJSON=ed,cf.locale=Bb,cf.localeData=Cb,cf.toIsoString=u("toIsoString() is deprecated. Please use toISOString() instead (notice the capitals)",ed),cf.lang=ne,R("X",0,0,"unix"),R("x",0,0,"valueOf"),W("x",Fd),W("X",Id),$("X",function(a,b,c){c._d=new Date(1e3*parseFloat(a,10))}),$("x",function(a,b,c){c._d=new Date(r(a))}),a.version="2.13.0",b(Ka),a.fn=De,a.min=Ma,a.max=Na,a.now=he,a.utc=h,a.unix=xc,a.months=Jc,a.isDate=d,a.locale=E,a.invalid=l,a.duration=db,a.isMoment=p,a.weekdays=Lc,a.parseZone=yc,a.localeData=H,a.isDuration=Pa,a.monthsShort=Kc,a.weekdaysMin=Nc,a.defineLocale=F,a.updateLocale=G,a.locales=I,a.weekdaysShort=Mc,a.normalizeUnits=K,a.relativeTimeThreshold=cd,a.prototype=De;var df=a;return df});
/*!
 * Bootstrap v3.3.5 (http://getbootstrap.com)
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under the MIT license
 */
if("undefined"==typeof jQuery)throw new Error("Bootstrap's JavaScript requires jQuery");+function(a){"use strict";var b=a.fn.jquery.split(" ")[0].split(".");if(b[0]<2&&b[1]<9||1==b[0]&&9==b[1]&&b[2]<1)throw new Error("Bootstrap's JavaScript requires jQuery version 1.9.1 or higher")}(jQuery),+function(a){"use strict";function b(){var a=document.createElement("bootstrap"),b={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var c in b)if(void 0!==a.style[c])return{end:b[c]};return!1}a.fn.emulateTransitionEnd=function(b){var c=!1,d=this;a(this).one("bsTransitionEnd",function(){c=!0});var e=function(){c||a(d).trigger(a.support.transition.end)};return setTimeout(e,b),this},a(function(){a.support.transition=b(),a.support.transition&&(a.event.special.bsTransitionEnd={bindType:a.support.transition.end,delegateType:a.support.transition.end,handle:function(b){return a(b.target).is(this)?b.handleObj.handler.apply(this,arguments):void 0}})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var c=a(this),e=c.data("bs.alert");e||c.data("bs.alert",e=new d(this)),"string"==typeof b&&e[b].call(c)})}var c='[data-dismiss="alert"]',d=function(b){a(b).on("click",c,this.close)};d.VERSION="3.3.5",d.TRANSITION_DURATION=150,d.prototype.close=function(b){function c(){g.detach().trigger("closed.bs.alert").remove()}var e=a(this),f=e.attr("data-target");f||(f=e.attr("href"),f=f&&f.replace(/.*(?=#[^\s]*$)/,""));var g=a(f);b&&b.preventDefault(),g.length||(g=e.closest(".alert")),g.trigger(b=a.Event("close.bs.alert")),b.isDefaultPrevented()||(g.removeClass("in"),a.support.transition&&g.hasClass("fade")?g.one("bsTransitionEnd",c).emulateTransitionEnd(d.TRANSITION_DURATION):c())};var e=a.fn.alert;a.fn.alert=b,a.fn.alert.Constructor=d,a.fn.alert.noConflict=function(){return a.fn.alert=e,this},a(document).on("click.bs.alert.data-api",c,d.prototype.close)}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.button"),f="object"==typeof b&&b;e||d.data("bs.button",e=new c(this,f)),"toggle"==b?e.toggle():b&&e.setState(b)})}var c=function(b,d){this.$element=a(b),this.options=a.extend({},c.DEFAULTS,d),this.isLoading=!1};c.VERSION="3.3.5",c.DEFAULTS={loadingText:"loading..."},c.prototype.setState=function(b){var c="disabled",d=this.$element,e=d.is("input")?"val":"html",f=d.data();b+="Text",null==f.resetText&&d.data("resetText",d[e]()),setTimeout(a.proxy(function(){d[e](null==f[b]?this.options[b]:f[b]),"loadingText"==b?(this.isLoading=!0,d.addClass(c).attr(c,c)):this.isLoading&&(this.isLoading=!1,d.removeClass(c).removeAttr(c))},this),0)},c.prototype.toggle=function(){var a=!0,b=this.$element.closest('[data-toggle="buttons"]');if(b.length){var c=this.$element.find("input");"radio"==c.prop("type")?(c.prop("checked")&&(a=!1),b.find(".active").removeClass("active"),this.$element.addClass("active")):"checkbox"==c.prop("type")&&(c.prop("checked")!==this.$element.hasClass("active")&&(a=!1),this.$element.toggleClass("active")),c.prop("checked",this.$element.hasClass("active")),a&&c.trigger("change")}else this.$element.attr("aria-pressed",!this.$element.hasClass("active")),this.$element.toggleClass("active")};var d=a.fn.button;a.fn.button=b,a.fn.button.Constructor=c,a.fn.button.noConflict=function(){return a.fn.button=d,this},a(document).on("click.bs.button.data-api",'[data-toggle^="button"]',function(c){var d=a(c.target);d.hasClass("btn")||(d=d.closest(".btn")),b.call(d,"toggle"),a(c.target).is('input[type="radio"]')||a(c.target).is('input[type="checkbox"]')||c.preventDefault()}).on("focus.bs.button.data-api blur.bs.button.data-api",'[data-toggle^="button"]',function(b){a(b.target).closest(".btn").toggleClass("focus",/^focus(in)?$/.test(b.type))})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.carousel"),f=a.extend({},c.DEFAULTS,d.data(),"object"==typeof b&&b),g="string"==typeof b?b:f.slide;e||d.data("bs.carousel",e=new c(this,f)),"number"==typeof b?e.to(b):g?e[g]():f.interval&&e.pause().cycle()})}var c=function(b,c){this.$element=a(b),this.$indicators=this.$element.find(".carousel-indicators"),this.options=c,this.paused=null,this.sliding=null,this.interval=null,this.$active=null,this.$items=null,this.options.keyboard&&this.$element.on("keydown.bs.carousel",a.proxy(this.keydown,this)),"hover"==this.options.pause&&!("ontouchstart"in document.documentElement)&&this.$element.on("mouseenter.bs.carousel",a.proxy(this.pause,this)).on("mouseleave.bs.carousel",a.proxy(this.cycle,this))};c.VERSION="3.3.5",c.TRANSITION_DURATION=600,c.DEFAULTS={interval:5e3,pause:"hover",wrap:!0,keyboard:!0},c.prototype.keydown=function(a){if(!/input|textarea/i.test(a.target.tagName)){switch(a.which){case 37:this.prev();break;case 39:this.next();break;default:return}a.preventDefault()}},c.prototype.cycle=function(b){return b||(this.paused=!1),this.interval&&clearInterval(this.interval),this.options.interval&&!this.paused&&(this.interval=setInterval(a.proxy(this.next,this),this.options.interval)),this},c.prototype.getItemIndex=function(a){return this.$items=a.parent().children(".item"),this.$items.index(a||this.$active)},c.prototype.getItemForDirection=function(a,b){var c=this.getItemIndex(b),d="prev"==a&&0===c||"next"==a&&c==this.$items.length-1;if(d&&!this.options.wrap)return b;var e="prev"==a?-1:1,f=(c+e)%this.$items.length;return this.$items.eq(f)},c.prototype.to=function(a){var b=this,c=this.getItemIndex(this.$active=this.$element.find(".item.active"));return a>this.$items.length-1||0>a?void 0:this.sliding?this.$element.one("slid.bs.carousel",function(){b.to(a)}):c==a?this.pause().cycle():this.slide(a>c?"next":"prev",this.$items.eq(a))},c.prototype.pause=function(b){return b||(this.paused=!0),this.$element.find(".next, .prev").length&&a.support.transition&&(this.$element.trigger(a.support.transition.end),this.cycle(!0)),this.interval=clearInterval(this.interval),this},c.prototype.next=function(){return this.sliding?void 0:this.slide("next")},c.prototype.prev=function(){return this.sliding?void 0:this.slide("prev")},c.prototype.slide=function(b,d){var e=this.$element.find(".item.active"),f=d||this.getItemForDirection(b,e),g=this.interval,h="next"==b?"left":"right",i=this;if(f.hasClass("active"))return this.sliding=!1;var j=f[0],k=a.Event("slide.bs.carousel",{relatedTarget:j,direction:h});if(this.$element.trigger(k),!k.isDefaultPrevented()){if(this.sliding=!0,g&&this.pause(),this.$indicators.length){this.$indicators.find(".active").removeClass("active");var l=a(this.$indicators.children()[this.getItemIndex(f)]);l&&l.addClass("active")}var m=a.Event("slid.bs.carousel",{relatedTarget:j,direction:h});return a.support.transition&&this.$element.hasClass("slide")?(f.addClass(b),f[0].offsetWidth,e.addClass(h),f.addClass(h),e.one("bsTransitionEnd",function(){f.removeClass([b,h].join(" ")).addClass("active"),e.removeClass(["active",h].join(" ")),i.sliding=!1,setTimeout(function(){i.$element.trigger(m)},0)}).emulateTransitionEnd(c.TRANSITION_DURATION)):(e.removeClass("active"),f.addClass("active"),this.sliding=!1,this.$element.trigger(m)),g&&this.cycle(),this}};var d=a.fn.carousel;a.fn.carousel=b,a.fn.carousel.Constructor=c,a.fn.carousel.noConflict=function(){return a.fn.carousel=d,this};var e=function(c){var d,e=a(this),f=a(e.attr("data-target")||(d=e.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,""));if(f.hasClass("carousel")){var g=a.extend({},f.data(),e.data()),h=e.attr("data-slide-to");h&&(g.interval=!1),b.call(f,g),h&&f.data("bs.carousel").to(h),c.preventDefault()}};a(document).on("click.bs.carousel.data-api","[data-slide]",e).on("click.bs.carousel.data-api","[data-slide-to]",e),a(window).on("load",function(){a('[data-ride="carousel"]').each(function(){var c=a(this);b.call(c,c.data())})})}(jQuery),+function(a){"use strict";function b(b){var c,d=b.attr("data-target")||(c=b.attr("href"))&&c.replace(/.*(?=#[^\s]+$)/,"");return a(d)}function c(b){return this.each(function(){var c=a(this),e=c.data("bs.collapse"),f=a.extend({},d.DEFAULTS,c.data(),"object"==typeof b&&b);!e&&f.toggle&&/show|hide/.test(b)&&(f.toggle=!1),e||c.data("bs.collapse",e=new d(this,f)),"string"==typeof b&&e[b]()})}var d=function(b,c){this.$element=a(b),this.options=a.extend({},d.DEFAULTS,c),this.$trigger=a('[data-toggle="collapse"][href="#'+b.id+'"],[data-toggle="collapse"][data-target="#'+b.id+'"]'),this.transitioning=null,this.options.parent?this.$parent=this.getParent():this.addAriaAndCollapsedClass(this.$element,this.$trigger),this.options.toggle&&this.toggle()};d.VERSION="3.3.5",d.TRANSITION_DURATION=350,d.DEFAULTS={toggle:!0},d.prototype.dimension=function(){var a=this.$element.hasClass("width");return a?"width":"height"},d.prototype.show=function(){if(!this.transitioning&&!this.$element.hasClass("in")){var b,e=this.$parent&&this.$parent.children(".panel").children(".in, .collapsing");if(!(e&&e.length&&(b=e.data("bs.collapse"),b&&b.transitioning))){var f=a.Event("show.bs.collapse");if(this.$element.trigger(f),!f.isDefaultPrevented()){e&&e.length&&(c.call(e,"hide"),b||e.data("bs.collapse",null));var g=this.dimension();this.$element.removeClass("collapse").addClass("collapsing")[g](0).attr("aria-expanded",!0),this.$trigger.removeClass("collapsed").attr("aria-expanded",!0),this.transitioning=1;var h=function(){this.$element.removeClass("collapsing").addClass("collapse in")[g](""),this.transitioning=0,this.$element.trigger("shown.bs.collapse")};if(!a.support.transition)return h.call(this);var i=a.camelCase(["scroll",g].join("-"));this.$element.one("bsTransitionEnd",a.proxy(h,this)).emulateTransitionEnd(d.TRANSITION_DURATION)[g](this.$element[0][i])}}}},d.prototype.hide=function(){if(!this.transitioning&&this.$element.hasClass("in")){var b=a.Event("hide.bs.collapse");if(this.$element.trigger(b),!b.isDefaultPrevented()){var c=this.dimension();this.$element[c](this.$element[c]())[0].offsetHeight,this.$element.addClass("collapsing").removeClass("collapse in").attr("aria-expanded",!1),this.$trigger.addClass("collapsed").attr("aria-expanded",!1),this.transitioning=1;var e=function(){this.transitioning=0,this.$element.removeClass("collapsing").addClass("collapse").trigger("hidden.bs.collapse")};return a.support.transition?void this.$element[c](0).one("bsTransitionEnd",a.proxy(e,this)).emulateTransitionEnd(d.TRANSITION_DURATION):e.call(this)}}},d.prototype.toggle=function(){this[this.$element.hasClass("in")?"hide":"show"]()},d.prototype.getParent=function(){return a(this.options.parent).find('[data-toggle="collapse"][data-parent="'+this.options.parent+'"]').each(a.proxy(function(c,d){var e=a(d);this.addAriaAndCollapsedClass(b(e),e)},this)).end()},d.prototype.addAriaAndCollapsedClass=function(a,b){var c=a.hasClass("in");a.attr("aria-expanded",c),b.toggleClass("collapsed",!c).attr("aria-expanded",c)};var e=a.fn.collapse;a.fn.collapse=c,a.fn.collapse.Constructor=d,a.fn.collapse.noConflict=function(){return a.fn.collapse=e,this},a(document).on("click.bs.collapse.data-api",'[data-toggle="collapse"]',function(d){var e=a(this);e.attr("data-target")||d.preventDefault();var f=b(e),g=f.data("bs.collapse"),h=g?"toggle":e.data();c.call(f,h)})}(jQuery),+function(a){"use strict";function b(b){var c=b.attr("data-target");c||(c=b.attr("href"),c=c&&/#[A-Za-z]/.test(c)&&c.replace(/.*(?=#[^\s]*$)/,""));var d=c&&a(c);return d&&d.length?d:b.parent()}function c(c){c&&3===c.which||(a(e).remove(),a(f).each(function(){var d=a(this),e=b(d),f={relatedTarget:this};e.hasClass("open")&&(c&&"click"==c.type&&/input|textarea/i.test(c.target.tagName)&&a.contains(e[0],c.target)||(e.trigger(c=a.Event("hide.bs.dropdown",f)),c.isDefaultPrevented()||(d.attr("aria-expanded","false"),e.removeClass("open").trigger("hidden.bs.dropdown",f))))}))}function d(b){return this.each(function(){var c=a(this),d=c.data("bs.dropdown");d||c.data("bs.dropdown",d=new g(this)),"string"==typeof b&&d[b].call(c)})}var e=".dropdown-backdrop",f='[data-toggle="dropdown"]',g=function(b){a(b).on("click.bs.dropdown",this.toggle)};g.VERSION="3.3.5",g.prototype.toggle=function(d){var e=a(this);if(!e.is(".disabled, :disabled")){var f=b(e),g=f.hasClass("open");if(c(),!g){"ontouchstart"in document.documentElement&&!f.closest(".navbar-nav").length&&a(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(a(this)).on("click",c);var h={relatedTarget:this};if(f.trigger(d=a.Event("show.bs.dropdown",h)),d.isDefaultPrevented())return;e.trigger("focus").attr("aria-expanded","true"),f.toggleClass("open").trigger("shown.bs.dropdown",h)}return!1}},g.prototype.keydown=function(c){if(/(38|40|27|32)/.test(c.which)&&!/input|textarea/i.test(c.target.tagName)){var d=a(this);if(c.preventDefault(),c.stopPropagation(),!d.is(".disabled, :disabled")){var e=b(d),g=e.hasClass("open");if(!g&&27!=c.which||g&&27==c.which)return 27==c.which&&e.find(f).trigger("focus"),d.trigger("click");var h=" li:not(.disabled):visible a",i=e.find(".dropdown-menu"+h);if(i.length){var j=i.index(c.target);38==c.which&&j>0&&j--,40==c.which&&j<i.length-1&&j++,~j||(j=0),i.eq(j).trigger("focus")}}}};var h=a.fn.dropdown;a.fn.dropdown=d,a.fn.dropdown.Constructor=g,a.fn.dropdown.noConflict=function(){return a.fn.dropdown=h,this},a(document).on("click.bs.dropdown.data-api",c).on("click.bs.dropdown.data-api",".dropdown form",function(a){a.stopPropagation()}).on("click.bs.dropdown.data-api",f,g.prototype.toggle).on("keydown.bs.dropdown.data-api",f,g.prototype.keydown).on("keydown.bs.dropdown.data-api",".dropdown-menu",g.prototype.keydown)}(jQuery),+function(a){"use strict";function b(b,d){return this.each(function(){var e=a(this),f=e.data("bs.modal"),g=a.extend({},c.DEFAULTS,e.data(),"object"==typeof b&&b);f||e.data("bs.modal",f=new c(this,g)),"string"==typeof b?f[b](d):g.show&&f.show(d)})}var c=function(b,c){this.options=c,this.$body=a(document.body),this.$element=a(b),this.$dialog=this.$element.find(".modal-dialog"),this.$backdrop=null,this.isShown=null,this.originalBodyPad=null,this.scrollbarWidth=0,this.ignoreBackdropClick=!1,this.options.remote&&this.$element.find(".modal-content").load(this.options.remote,a.proxy(function(){this.$element.trigger("loaded.bs.modal")},this))};c.VERSION="3.3.5",c.TRANSITION_DURATION=300,c.BACKDROP_TRANSITION_DURATION=150,c.DEFAULTS={backdrop:!0,keyboard:!0,show:!0},c.prototype.toggle=function(a){return this.isShown?this.hide():this.show(a)},c.prototype.show=function(b){var d=this,e=a.Event("show.bs.modal",{relatedTarget:b});this.$element.trigger(e),this.isShown||e.isDefaultPrevented()||(this.isShown=!0,this.checkScrollbar(),this.setScrollbar(),this.$body.addClass("modal-open"),this.escape(),this.resize(),this.$element.on("click.dismiss.bs.modal",'[data-dismiss="modal"]',a.proxy(this.hide,this)),this.$dialog.on("mousedown.dismiss.bs.modal",function(){d.$element.one("mouseup.dismiss.bs.modal",function(b){a(b.target).is(d.$element)&&(d.ignoreBackdropClick=!0)})}),this.backdrop(function(){var e=a.support.transition&&d.$element.hasClass("fade");d.$element.parent().length||d.$element.appendTo(d.$body),d.$element.show().scrollTop(0),d.adjustDialog(),e&&d.$element[0].offsetWidth,d.$element.addClass("in"),d.enforceFocus();var f=a.Event("shown.bs.modal",{relatedTarget:b});e?d.$dialog.one("bsTransitionEnd",function(){d.$element.trigger("focus").trigger(f)}).emulateTransitionEnd(c.TRANSITION_DURATION):d.$element.trigger("focus").trigger(f)}))},c.prototype.hide=function(b){b&&b.preventDefault(),b=a.Event("hide.bs.modal"),this.$element.trigger(b),this.isShown&&!b.isDefaultPrevented()&&(this.isShown=!1,this.escape(),this.resize(),a(document).off("focusin.bs.modal"),this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"),this.$dialog.off("mousedown.dismiss.bs.modal"),a.support.transition&&this.$element.hasClass("fade")?this.$element.one("bsTransitionEnd",a.proxy(this.hideModal,this)).emulateTransitionEnd(c.TRANSITION_DURATION):this.hideModal())},c.prototype.enforceFocus=function(){a(document).off("focusin.bs.modal").on("focusin.bs.modal",a.proxy(function(a){this.$element[0]===a.target||this.$element.has(a.target).length||this.$element.trigger("focus")},this))},c.prototype.escape=function(){this.isShown&&this.options.keyboard?this.$element.on("keydown.dismiss.bs.modal",a.proxy(function(a){27==a.which&&this.hide()},this)):this.isShown||this.$element.off("keydown.dismiss.bs.modal")},c.prototype.resize=function(){this.isShown?a(window).on("resize.bs.modal",a.proxy(this.handleUpdate,this)):a(window).off("resize.bs.modal")},c.prototype.hideModal=function(){var a=this;this.$element.hide(),this.backdrop(function(){a.$body.removeClass("modal-open"),a.resetAdjustments(),a.resetScrollbar(),a.$element.trigger("hidden.bs.modal")})},c.prototype.removeBackdrop=function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},c.prototype.backdrop=function(b){var d=this,e=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var f=a.support.transition&&e;if(this.$backdrop=a(document.createElement("div")).addClass("modal-backdrop "+e).appendTo(this.$body),this.$element.on("click.dismiss.bs.modal",a.proxy(function(a){return this.ignoreBackdropClick?void(this.ignoreBackdropClick=!1):void(a.target===a.currentTarget&&("static"==this.options.backdrop?this.$element[0].focus():this.hide()))},this)),f&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),!b)return;f?this.$backdrop.one("bsTransitionEnd",b).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION):b()}else if(!this.isShown&&this.$backdrop){this.$backdrop.removeClass("in");var g=function(){d.removeBackdrop(),b&&b()};a.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one("bsTransitionEnd",g).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION):g()}else b&&b()},c.prototype.handleUpdate=function(){this.adjustDialog()},c.prototype.adjustDialog=function(){var a=this.$element[0].scrollHeight>document.documentElement.clientHeight;this.$element.css({paddingLeft:!this.bodyIsOverflowing&&a?this.scrollbarWidth:"",paddingRight:this.bodyIsOverflowing&&!a?this.scrollbarWidth:""})},c.prototype.resetAdjustments=function(){this.$element.css({paddingLeft:"",paddingRight:""})},c.prototype.checkScrollbar=function(){var a=window.innerWidth;if(!a){var b=document.documentElement.getBoundingClientRect();a=b.right-Math.abs(b.left)}this.bodyIsOverflowing=document.body.clientWidth<a,this.scrollbarWidth=this.measureScrollbar()},c.prototype.setScrollbar=function(){var a=parseInt(this.$body.css("padding-right")||0,10);this.originalBodyPad=document.body.style.paddingRight||"",this.bodyIsOverflowing&&this.$body.css("padding-right",a+this.scrollbarWidth)},c.prototype.resetScrollbar=function(){this.$body.css("padding-right",this.originalBodyPad)},c.prototype.measureScrollbar=function(){var a=document.createElement("div");a.className="modal-scrollbar-measure",this.$body.append(a);var b=a.offsetWidth-a.clientWidth;return this.$body[0].removeChild(a),b};var d=a.fn.modal;a.fn.modal=b,a.fn.modal.Constructor=c,a.fn.modal.noConflict=function(){return a.fn.modal=d,this},a(document).on("click.bs.modal.data-api",'[data-toggle="modal"]',function(c){var d=a(this),e=d.attr("href"),f=a(d.attr("data-target")||e&&e.replace(/.*(?=#[^\s]+$)/,"")),g=f.data("bs.modal")?"toggle":a.extend({remote:!/#/.test(e)&&e},f.data(),d.data());d.is("a")&&c.preventDefault(),f.one("show.bs.modal",function(a){a.isDefaultPrevented()||f.one("hidden.bs.modal",function(){d.is(":visible")&&d.trigger("focus")})}),b.call(f,g,this)})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.tooltip"),f="object"==typeof b&&b;(e||!/destroy|hide/.test(b))&&(e||d.data("bs.tooltip",e=new c(this,f)),"string"==typeof b&&e[b]())})}var c=function(a,b){this.type=null,this.options=null,this.enabled=null,this.timeout=null,this.hoverState=null,this.$element=null,this.inState=null,this.init("tooltip",a,b)};c.VERSION="3.3.5",c.TRANSITION_DURATION=150,c.DEFAULTS={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,container:!1,viewport:{selector:"body",padding:0}},c.prototype.init=function(b,c,d){if(this.enabled=!0,this.type=b,this.$element=a(c),this.options=this.getOptions(d),this.$viewport=this.options.viewport&&a(a.isFunction(this.options.viewport)?this.options.viewport.call(this,this.$element):this.options.viewport.selector||this.options.viewport),this.inState={click:!1,hover:!1,focus:!1},this.$element[0]instanceof document.constructor&&!this.options.selector)throw new Error("`selector` option must be specified when initializing "+this.type+" on the window.document object!");for(var e=this.options.trigger.split(" "),f=e.length;f--;){var g=e[f];if("click"==g)this.$element.on("click."+this.type,this.options.selector,a.proxy(this.toggle,this));else if("manual"!=g){var h="hover"==g?"mouseenter":"focusin",i="hover"==g?"mouseleave":"focusout";this.$element.on(h+"."+this.type,this.options.selector,a.proxy(this.enter,this)),this.$element.on(i+"."+this.type,this.options.selector,a.proxy(this.leave,this))}}this.options.selector?this._options=a.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},c.prototype.getDefaults=function(){return c.DEFAULTS},c.prototype.getOptions=function(b){return b=a.extend({},this.getDefaults(),this.$element.data(),b),b.delay&&"number"==typeof b.delay&&(b.delay={show:b.delay,hide:b.delay}),b},c.prototype.getDelegateOptions=function(){var b={},c=this.getDefaults();return this._options&&a.each(this._options,function(a,d){c[a]!=d&&(b[a]=d)}),b},c.prototype.enter=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget).data("bs."+this.type);return c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),b instanceof a.Event&&(c.inState["focusin"==b.type?"focus":"hover"]=!0),c.tip().hasClass("in")||"in"==c.hoverState?void(c.hoverState="in"):(clearTimeout(c.timeout),c.hoverState="in",c.options.delay&&c.options.delay.show?void(c.timeout=setTimeout(function(){"in"==c.hoverState&&c.show()},c.options.delay.show)):c.show())},c.prototype.isInStateTrue=function(){for(var a in this.inState)if(this.inState[a])return!0;return!1},c.prototype.leave=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget).data("bs."+this.type);return c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),b instanceof a.Event&&(c.inState["focusout"==b.type?"focus":"hover"]=!1),c.isInStateTrue()?void 0:(clearTimeout(c.timeout),c.hoverState="out",c.options.delay&&c.options.delay.hide?void(c.timeout=setTimeout(function(){"out"==c.hoverState&&c.hide()},c.options.delay.hide)):c.hide())},c.prototype.show=function(){var b=a.Event("show.bs."+this.type);if(this.hasContent()&&this.enabled){this.$element.trigger(b);var d=a.contains(this.$element[0].ownerDocument.documentElement,this.$element[0]);if(b.isDefaultPrevented()||!d)return;var e=this,f=this.tip(),g=this.getUID(this.type);this.setContent(),f.attr("id",g),this.$element.attr("aria-describedby",g),this.options.animation&&f.addClass("fade");var h="function"==typeof this.options.placement?this.options.placement.call(this,f[0],this.$element[0]):this.options.placement,i=/\s?auto?\s?/i,j=i.test(h);j&&(h=h.replace(i,"")||"top"),f.detach().css({top:0,left:0,display:"block"}).addClass(h).data("bs."+this.type,this),this.options.container?f.appendTo(this.options.container):f.insertAfter(this.$element),this.$element.trigger("inserted.bs."+this.type);var k=this.getPosition(),l=f[0].offsetWidth,m=f[0].offsetHeight;if(j){var n=h,o=this.getPosition(this.$viewport);h="bottom"==h&&k.bottom+m>o.bottom?"top":"top"==h&&k.top-m<o.top?"bottom":"right"==h&&k.right+l>o.width?"left":"left"==h&&k.left-l<o.left?"right":h,f.removeClass(n).addClass(h)}var p=this.getCalculatedOffset(h,k,l,m);this.applyPlacement(p,h);var q=function(){var a=e.hoverState;e.$element.trigger("shown.bs."+e.type),e.hoverState=null,"out"==a&&e.leave(e)};a.support.transition&&this.$tip.hasClass("fade")?f.one("bsTransitionEnd",q).emulateTransitionEnd(c.TRANSITION_DURATION):q()}},c.prototype.applyPlacement=function(b,c){var d=this.tip(),e=d[0].offsetWidth,f=d[0].offsetHeight,g=parseInt(d.css("margin-top"),10),h=parseInt(d.css("margin-left"),10);isNaN(g)&&(g=0),isNaN(h)&&(h=0),b.top+=g,b.left+=h,a.offset.setOffset(d[0],a.extend({using:function(a){d.css({top:Math.round(a.top),left:Math.round(a.left)})}},b),0),d.addClass("in");var i=d[0].offsetWidth,j=d[0].offsetHeight;"top"==c&&j!=f&&(b.top=b.top+f-j);var k=this.getViewportAdjustedDelta(c,b,i,j);k.left?b.left+=k.left:b.top+=k.top;var l=/top|bottom/.test(c),m=l?2*k.left-e+i:2*k.top-f+j,n=l?"offsetWidth":"offsetHeight";d.offset(b),this.replaceArrow(m,d[0][n],l)},c.prototype.replaceArrow=function(a,b,c){this.arrow().css(c?"left":"top",50*(1-a/b)+"%").css(c?"top":"left","")},c.prototype.setContent=function(){var a=this.tip(),b=this.getTitle();a.find(".tooltip-inner")[this.options.html?"html":"text"](b),a.removeClass("fade in top bottom left right")},c.prototype.hide=function(b){function d(){"in"!=e.hoverState&&f.detach(),e.$element.removeAttr("aria-describedby").trigger("hidden.bs."+e.type),b&&b()}var e=this,f=a(this.$tip),g=a.Event("hide.bs."+this.type);return this.$element.trigger(g),g.isDefaultPrevented()?void 0:(f.removeClass("in"),a.support.transition&&f.hasClass("fade")?f.one("bsTransitionEnd",d).emulateTransitionEnd(c.TRANSITION_DURATION):d(),this.hoverState=null,this)},c.prototype.fixTitle=function(){var a=this.$element;(a.attr("title")||"string"!=typeof a.attr("data-original-title"))&&a.attr("data-original-title",a.attr("title")||"").attr("title","")},c.prototype.hasContent=function(){return this.getTitle()},c.prototype.getPosition=function(b){b=b||this.$element;var c=b[0],d="BODY"==c.tagName,e=c.getBoundingClientRect();null==e.width&&(e=a.extend({},e,{width:e.right-e.left,height:e.bottom-e.top}));var f=d?{top:0,left:0}:b.offset(),g={scroll:d?document.documentElement.scrollTop||document.body.scrollTop:b.scrollTop()},h=d?{width:a(window).width(),height:a(window).height()}:null;return a.extend({},e,g,h,f)},c.prototype.getCalculatedOffset=function(a,b,c,d){return"bottom"==a?{top:b.top+b.height,left:b.left+b.width/2-c/2}:"top"==a?{top:b.top-d,left:b.left+b.width/2-c/2}:"left"==a?{top:b.top+b.height/2-d/2,left:b.left-c}:{top:b.top+b.height/2-d/2,left:b.left+b.width}},c.prototype.getViewportAdjustedDelta=function(a,b,c,d){var e={top:0,left:0};if(!this.$viewport)return e;var f=this.options.viewport&&this.options.viewport.padding||0,g=this.getPosition(this.$viewport);if(/right|left/.test(a)){var h=b.top-f-g.scroll,i=b.top+f-g.scroll+d;h<g.top?e.top=g.top-h:i>g.top+g.height&&(e.top=g.top+g.height-i)}else{var j=b.left-f,k=b.left+f+c;j<g.left?e.left=g.left-j:k>g.right&&(e.left=g.left+g.width-k)}return e},c.prototype.getTitle=function(){var a,b=this.$element,c=this.options;return a=b.attr("data-original-title")||("function"==typeof c.title?c.title.call(b[0]):c.title)},c.prototype.getUID=function(a){do a+=~~(1e6*Math.random());while(document.getElementById(a));return a},c.prototype.tip=function(){if(!this.$tip&&(this.$tip=a(this.options.template),1!=this.$tip.length))throw new Error(this.type+" `template` option must consist of exactly 1 top-level element!");return this.$tip},c.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},c.prototype.enable=function(){this.enabled=!0},c.prototype.disable=function(){this.enabled=!1},c.prototype.toggleEnabled=function(){this.enabled=!this.enabled},c.prototype.toggle=function(b){var c=this;b&&(c=a(b.currentTarget).data("bs."+this.type),c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c))),b?(c.inState.click=!c.inState.click,c.isInStateTrue()?c.enter(c):c.leave(c)):c.tip().hasClass("in")?c.leave(c):c.enter(c)},c.prototype.destroy=function(){var a=this;clearTimeout(this.timeout),this.hide(function(){a.$element.off("."+a.type).removeData("bs."+a.type),a.$tip&&a.$tip.detach(),a.$tip=null,a.$arrow=null,a.$viewport=null})};var d=a.fn.tooltip;a.fn.tooltip=b,a.fn.tooltip.Constructor=c,a.fn.tooltip.noConflict=function(){return a.fn.tooltip=d,this}}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.popover"),f="object"==typeof b&&b;(e||!/destroy|hide/.test(b))&&(e||d.data("bs.popover",e=new c(this,f)),"string"==typeof b&&e[b]())})}var c=function(a,b){this.init("popover",a,b)};if(!a.fn.tooltip)throw new Error("Popover requires tooltip.js");c.VERSION="3.3.5",c.DEFAULTS=a.extend({},a.fn.tooltip.Constructor.DEFAULTS,{placement:"right",trigger:"click",content:"",template:'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'}),c.prototype=a.extend({},a.fn.tooltip.Constructor.prototype),c.prototype.constructor=c,c.prototype.getDefaults=function(){return c.DEFAULTS},c.prototype.setContent=function(){var a=this.tip(),b=this.getTitle(),c=this.getContent();a.find(".popover-title")[this.options.html?"html":"text"](b),a.find(".popover-content").children().detach().end()[this.options.html?"string"==typeof c?"html":"append":"text"](c),a.removeClass("fade top bottom left right in"),a.find(".popover-title").html()||a.find(".popover-title").hide()},c.prototype.hasContent=function(){return this.getTitle()||this.getContent()},c.prototype.getContent=function(){var a=this.$element,b=this.options;return a.attr("data-content")||("function"==typeof b.content?b.content.call(a[0]):b.content)},c.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".arrow")};var d=a.fn.popover;a.fn.popover=b,a.fn.popover.Constructor=c,a.fn.popover.noConflict=function(){return a.fn.popover=d,this}}(jQuery),+function(a){"use strict";function b(c,d){this.$body=a(document.body),this.$scrollElement=a(a(c).is(document.body)?window:c),this.options=a.extend({},b.DEFAULTS,d),this.selector=(this.options.target||"")+" .nav li > a",this.offsets=[],this.targets=[],this.activeTarget=null,this.scrollHeight=0,this.$scrollElement.on("scroll.bs.scrollspy",a.proxy(this.process,this)),this.refresh(),this.process()}function c(c){return this.each(function(){var d=a(this),e=d.data("bs.scrollspy"),f="object"==typeof c&&c;e||d.data("bs.scrollspy",e=new b(this,f)),"string"==typeof c&&e[c]()})}b.VERSION="3.3.5",b.DEFAULTS={offset:10},b.prototype.getScrollHeight=function(){return this.$scrollElement[0].scrollHeight||Math.max(this.$body[0].scrollHeight,document.documentElement.scrollHeight)},b.prototype.refresh=function(){var b=this,c="offset",d=0;this.offsets=[],this.targets=[],this.scrollHeight=this.getScrollHeight(),a.isWindow(this.$scrollElement[0])||(c="position",d=this.$scrollElement.scrollTop()),this.$body.find(this.selector).map(function(){var b=a(this),e=b.data("target")||b.attr("href"),f=/^#./.test(e)&&a(e);return f&&f.length&&f.is(":visible")&&[[f[c]().top+d,e]]||null}).sort(function(a,b){return a[0]-b[0]}).each(function(){b.offsets.push(this[0]),b.targets.push(this[1])})},b.prototype.process=function(){var a,b=this.$scrollElement.scrollTop()+this.options.offset,c=this.getScrollHeight(),d=this.options.offset+c-this.$scrollElement.height(),e=this.offsets,f=this.targets,g=this.activeTarget;if(this.scrollHeight!=c&&this.refresh(),b>=d)return g!=(a=f[f.length-1])&&this.activate(a);if(g&&b<e[0])return this.activeTarget=null,this.clear();for(a=e.length;a--;)g!=f[a]&&b>=e[a]&&(void 0===e[a+1]||b<e[a+1])&&this.activate(f[a])},b.prototype.activate=function(b){this.activeTarget=b,this.clear();var c=this.selector+'[data-target="'+b+'"],'+this.selector+'[href="'+b+'"]',d=a(c).parents("li").addClass("active");d.parent(".dropdown-menu").length&&(d=d.closest("li.dropdown").addClass("active")),
d.trigger("activate.bs.scrollspy")},b.prototype.clear=function(){a(this.selector).parentsUntil(this.options.target,".active").removeClass("active")};var d=a.fn.scrollspy;a.fn.scrollspy=c,a.fn.scrollspy.Constructor=b,a.fn.scrollspy.noConflict=function(){return a.fn.scrollspy=d,this},a(window).on("load.bs.scrollspy.data-api",function(){a('[data-spy="scroll"]').each(function(){var b=a(this);c.call(b,b.data())})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.tab");e||d.data("bs.tab",e=new c(this)),"string"==typeof b&&e[b]()})}var c=function(b){this.element=a(b)};c.VERSION="3.3.5",c.TRANSITION_DURATION=150,c.prototype.show=function(){var b=this.element,c=b.closest("ul:not(.dropdown-menu)"),d=b.data("target");if(d||(d=b.attr("href"),d=d&&d.replace(/.*(?=#[^\s]*$)/,"")),!b.parent("li").hasClass("active")){var e=c.find(".active:last a"),f=a.Event("hide.bs.tab",{relatedTarget:b[0]}),g=a.Event("show.bs.tab",{relatedTarget:e[0]});if(e.trigger(f),b.trigger(g),!g.isDefaultPrevented()&&!f.isDefaultPrevented()){var h=a(d);this.activate(b.closest("li"),c),this.activate(h,h.parent(),function(){e.trigger({type:"hidden.bs.tab",relatedTarget:b[0]}),b.trigger({type:"shown.bs.tab",relatedTarget:e[0]})})}}},c.prototype.activate=function(b,d,e){function f(){g.removeClass("active").find("> .dropdown-menu > .active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!1),b.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded",!0),h?(b[0].offsetWidth,b.addClass("in")):b.removeClass("fade"),b.parent(".dropdown-menu").length&&b.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!0),e&&e()}var g=d.find("> .active"),h=e&&a.support.transition&&(g.length&&g.hasClass("fade")||!!d.find("> .fade").length);g.length&&h?g.one("bsTransitionEnd",f).emulateTransitionEnd(c.TRANSITION_DURATION):f(),g.removeClass("in")};var d=a.fn.tab;a.fn.tab=b,a.fn.tab.Constructor=c,a.fn.tab.noConflict=function(){return a.fn.tab=d,this};var e=function(c){c.preventDefault(),b.call(a(this),"show")};a(document).on("click.bs.tab.data-api",'[data-toggle="tab"]',e).on("click.bs.tab.data-api",'[data-toggle="pill"]',e)}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.affix"),f="object"==typeof b&&b;e||d.data("bs.affix",e=new c(this,f)),"string"==typeof b&&e[b]()})}var c=function(b,d){this.options=a.extend({},c.DEFAULTS,d),this.$target=a(this.options.target).on("scroll.bs.affix.data-api",a.proxy(this.checkPosition,this)).on("click.bs.affix.data-api",a.proxy(this.checkPositionWithEventLoop,this)),this.$element=a(b),this.affixed=null,this.unpin=null,this.pinnedOffset=null,this.checkPosition()};c.VERSION="3.3.5",c.RESET="affix affix-top affix-bottom",c.DEFAULTS={offset:0,target:window},c.prototype.getState=function(a,b,c,d){var e=this.$target.scrollTop(),f=this.$element.offset(),g=this.$target.height();if(null!=c&&"top"==this.affixed)return c>e?"top":!1;if("bottom"==this.affixed)return null!=c?e+this.unpin<=f.top?!1:"bottom":a-d>=e+g?!1:"bottom";var h=null==this.affixed,i=h?e:f.top,j=h?g:b;return null!=c&&c>=e?"top":null!=d&&i+j>=a-d?"bottom":!1},c.prototype.getPinnedOffset=function(){if(this.pinnedOffset)return this.pinnedOffset;this.$element.removeClass(c.RESET).addClass("affix");var a=this.$target.scrollTop(),b=this.$element.offset();return this.pinnedOffset=b.top-a},c.prototype.checkPositionWithEventLoop=function(){setTimeout(a.proxy(this.checkPosition,this),1)},c.prototype.checkPosition=function(){if(this.$element.is(":visible")){var b=this.$element.height(),d=this.options.offset,e=d.top,f=d.bottom,g=Math.max(a(document).height(),a(document.body).height());"object"!=typeof d&&(f=e=d),"function"==typeof e&&(e=d.top(this.$element)),"function"==typeof f&&(f=d.bottom(this.$element));var h=this.getState(g,b,e,f);if(this.affixed!=h){null!=this.unpin&&this.$element.css("top","");var i="affix"+(h?"-"+h:""),j=a.Event(i+".bs.affix");if(this.$element.trigger(j),j.isDefaultPrevented())return;this.affixed=h,this.unpin="bottom"==h?this.getPinnedOffset():null,this.$element.removeClass(c.RESET).addClass(i).trigger(i.replace("affix","affixed")+".bs.affix")}"bottom"==h&&this.$element.offset({top:g-b-f})}};var d=a.fn.affix;a.fn.affix=b,a.fn.affix.Constructor=c,a.fn.affix.noConflict=function(){return a.fn.affix=d,this},a(window).on("load",function(){a('[data-spy="affix"]').each(function(){var c=a(this),d=c.data();d.offset=d.offset||{},null!=d.offsetBottom&&(d.offset.bottom=d.offsetBottom),null!=d.offsetTop&&(d.offset.top=d.offsetTop),b.call(c,d)})})}(jQuery);
/**
* @version: 2.1.23-beta
* @author: Dan Grossman http://www.dangrossman.info/
* @copyright: Copyright (c) 2012-2016 Dan Grossman. All rights reserved.
* @license: Licensed under the MIT license. See http://www.opensource.org/licenses/mit-license.php
* @website: https://www.improvely.com/
*/
// Follow the UMD template https://github.com/umdjs/umd/blob/master/templates/returnExportsGlobal.js
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Make globaly available as well
        define(['moment', 'jquery'], function (moment, jquery) {
            return (root.daterangepicker = factory(moment, jquery));
        });
    } else if (typeof module === 'object' && module.exports) {
        // Node / Browserify
        //isomorphic issue
        var jQuery = (typeof window != 'undefined') ? window.jQuery : undefined;
        if (!jQuery) {
            jQuery = require('jquery');
            if (!jQuery.fn) jQuery.fn = {};
        }
        module.exports = factory(require('moment'), jQuery);
    } else {
        // Browser globals
        root.daterangepicker = factory(root.moment, root.jQuery);
    }
}(this, function(moment, $) {
    var DateRangePicker = function(element, options, cb) {

        //default settings for options
        this.parentEl = 'body';
        this.element = $(element);
        this.startDate = moment().startOf('day');
        this.endDate = moment().endOf('day');
        this.minDate = false;
        this.maxDate = false;
        this.dateLimit = false;
        this.autoApply = false;
        this.singleDatePicker = false;
        this.showDropdowns = false;
        this.showWeekNumbers = false;
        this.showISOWeekNumbers = false;
        this.timePicker = false;
        this.timePicker24Hour = false;
        this.timePickerIncrement = 1;
        this.timePickerSeconds = false;
        this.linkedCalendars = true;
        this.autoUpdateInput = true;
        this.alwaysShowCalendars = false;
        this.ranges = {};

        this.opens = 'right';
        if (this.element.hasClass('pull-right'))
            this.opens = 'left';

        this.drops = 'down';
        if (this.element.hasClass('dropup'))
            this.drops = 'up';

        this.buttonClasses = 'btn btn-sm';
        this.applyClass = 'btn-success';
        this.cancelClass = 'btn-default';

        this.locale = {
            direction: 'ltr',
            format: 'MM/DD/YYYY',
            separator: ' - ',
            applyLabel: 'Apply',
            cancelLabel: 'Cancel',
            weekLabel: 'W',
            customRangeLabel: 'Custom Range',
            daysOfWeek: moment.weekdaysMin(),
            monthNames: moment.monthsShort(),
            firstDay: moment.localeData().firstDayOfWeek()
        };

        this.callback = function() { };

        //some state information
        this.isShowing = false;
        this.leftCalendar = {};
        this.rightCalendar = {};

        //custom options from user
        if (typeof options !== 'object' || options === null)
            options = {};

        //allow setting options with data attributes
        //data-api options will be overwritten with custom javascript options
        options = $.extend(this.element.data(), options);

        //html template for the picker UI
        if (typeof options.template !== 'string' && !(options.template instanceof $))
            options.template = '<div class="daterangepicker dropdown-menu">' +
                '<div class="calendar left">' +
                    '<div class="daterangepicker_input">' +
                      '<input class="input-mini form-control" type="text" name="daterangepicker_start" value="" />' +
                      '<i class="r-dateico"></i>' +
                      '<div class="calendar-time">' +
                        '<div></div>' +
                        '<i class="fa fa-clock-o glyphicon glyphicon-time"></i>' +
                      '</div>' +
                    '</div>' +
                    '<div class="calendar-table"></div>' +
                '</div>' +
                '<div class="calendar right">' +
                    '<div class="daterangepicker_input">' +
                      '<input class="input-mini form-control" type="text" name="daterangepicker_end" value="" />' +
                      '<i class="r-dateico"></i>' +
                      '<div class="calendar-time">' +
                        '<div></div>' +
                        '<i class="fa fa-clock-o glyphicon glyphicon-time"></i>' +
                      '</div>' +
                    '</div>' +
                    '<div class="calendar-table"></div>' +
                '</div>' +
                '<div class="ranges">' +
                    '<div class="range_inputs">' +
                        '<button class="applyBtn" disabled="disabled" type="button"></button> ' +
                        '<button class="cancelBtn" type="button"></button>' +
                    '</div>' +
                '</div>' +
            '</div>';

        this.parentEl = (options.parentEl && $(options.parentEl).length) ? $(options.parentEl) : $(this.parentEl);
        this.container = $(options.template).appendTo('.record-panel');

        //
        // handle all the possible options overriding defaults
        //

        if (typeof options.locale === 'object') {

            if (typeof options.locale.direction === 'string')
                this.locale.direction = options.locale.direction;

            if (typeof options.locale.format === 'string')
                this.locale.format = options.locale.format;

            if (typeof options.locale.separator === 'string')
                this.locale.separator = options.locale.separator;

            if (typeof options.locale.daysOfWeek === 'object')
                this.locale.daysOfWeek = options.locale.daysOfWeek.slice();

            if (typeof options.locale.monthNames === 'object')
              this.locale.monthNames = options.locale.monthNames.slice();

            if (typeof options.locale.firstDay === 'number')
              this.locale.firstDay = options.locale.firstDay;

            if (typeof options.locale.applyLabel === 'string')
              this.locale.applyLabel = options.locale.applyLabel;

            if (typeof options.locale.cancelLabel === 'string')
              this.locale.cancelLabel = options.locale.cancelLabel;

            if (typeof options.locale.weekLabel === 'string')
              this.locale.weekLabel = options.locale.weekLabel;

            if (typeof options.locale.customRangeLabel === 'string')
              this.locale.customRangeLabel = options.locale.customRangeLabel;

        }
        this.container.addClass(this.locale.direction);

        if (typeof options.startDate === 'string')
            this.startDate = moment(options.startDate, this.locale.format);

        if (typeof options.endDate === 'string')
            this.endDate = moment(options.endDate, this.locale.format);

        if (typeof options.minDate === 'string')
            this.minDate = moment(options.minDate, this.locale.format);

        if (typeof options.maxDate === 'string')
            this.maxDate = moment(options.maxDate, this.locale.format);

        if (typeof options.startDate === 'object')
            this.startDate = moment(options.startDate);

        if (typeof options.endDate === 'object')
            this.endDate = moment(options.endDate);

        if (typeof options.minDate === 'object')
            this.minDate = moment(options.minDate);

        if (typeof options.maxDate === 'object')
            this.maxDate = moment(options.maxDate);

        // sanity check for bad options
        if (this.minDate && this.startDate.isBefore(this.minDate))
            this.startDate = this.minDate.clone();

        // sanity check for bad options
        if (this.maxDate && this.endDate.isAfter(this.maxDate))
            this.endDate = this.maxDate.clone();

        if (typeof options.applyClass === 'string')
            this.applyClass = options.applyClass;

        if (typeof options.cancelClass === 'string')
            this.cancelClass = options.cancelClass;

        if (typeof options.dateLimit === 'object')
            this.dateLimit = options.dateLimit;

        if (typeof options.opens === 'string')
            this.opens = options.opens;

        if (typeof options.drops === 'string')
            this.drops = options.drops;

        if (typeof options.showWeekNumbers === 'boolean')
            this.showWeekNumbers = options.showWeekNumbers;

        if (typeof options.showISOWeekNumbers === 'boolean')
            this.showISOWeekNumbers = options.showISOWeekNumbers;

        if (typeof options.buttonClasses === 'string')
            this.buttonClasses = options.buttonClasses;

        if (typeof options.buttonClasses === 'object')
            this.buttonClasses = options.buttonClasses.join(' ');

        if (typeof options.showDropdowns === 'boolean')
            this.showDropdowns = options.showDropdowns;

        if (typeof options.singleDatePicker === 'boolean') {
            this.singleDatePicker = options.singleDatePicker;
            if (this.singleDatePicker)
                this.endDate = this.startDate.clone();
        }

        if (typeof options.timePicker === 'boolean')
            this.timePicker = options.timePicker;

        if (typeof options.timePickerSeconds === 'boolean')
            this.timePickerSeconds = options.timePickerSeconds;

        if (typeof options.timePickerIncrement === 'number')
            this.timePickerIncrement = options.timePickerIncrement;

        if (typeof options.timePicker24Hour === 'boolean')
            this.timePicker24Hour = options.timePicker24Hour;

        if (typeof options.autoApply === 'boolean')
            this.autoApply = options.autoApply;

        if (typeof options.autoUpdateInput === 'boolean')
            this.autoUpdateInput = options.autoUpdateInput;

        if (typeof options.linkedCalendars === 'boolean')
            this.linkedCalendars = options.linkedCalendars;

        if (typeof options.isInvalidDate === 'function')
            this.isInvalidDate = options.isInvalidDate;

        if (typeof options.isCustomDate === 'function')
            this.isCustomDate = options.isCustomDate;

        if (typeof options.alwaysShowCalendars === 'boolean')
            this.alwaysShowCalendars = options.alwaysShowCalendars;

        // update day names order to firstDay
        if (this.locale.firstDay != 0) {
            var iterator = this.locale.firstDay;
            while (iterator > 0) {
                this.locale.daysOfWeek.push(this.locale.daysOfWeek.shift());
                iterator--;
            }
        }

        var start, end, range;

        //if no start/end dates set, check if an input element contains initial values
        if (typeof options.startDate === 'undefined' && typeof options.endDate === 'undefined') {
            if ($(this.element).is('input[type=text]')) {
                var val = $(this.element).val(),
                    split = val.split(this.locale.separator);

                start = end = null;

                if (split.length == 2) {
                    start = moment(split[0], this.locale.format);
                    end = moment(split[1], this.locale.format);
                } else if (this.singleDatePicker && val !== "") {
                    start = moment(val, this.locale.format);
                    end = moment(val, this.locale.format);
                }
                if (start !== null && end !== null) {
                    this.setStartDate(start);
                    this.setEndDate(end);
                }
            }
        }

        if (typeof options.ranges === 'object') {
            for (range in options.ranges) {

                if (typeof options.ranges[range][0] === 'string')
                    start = moment(options.ranges[range][0], this.locale.format);
                else
                    start = moment(options.ranges[range][0]);

                if (typeof options.ranges[range][1] === 'string')
                    end = moment(options.ranges[range][1], this.locale.format);
                else
                    end = moment(options.ranges[range][1]);

                // If the start or end date exceed those allowed by the minDate or dateLimit
                // options, shorten the range to the allowable period.
                if (this.minDate && start.isBefore(this.minDate))
                    start = this.minDate.clone();

                var maxDate = this.maxDate;
                if (this.dateLimit && maxDate && start.clone().add(this.dateLimit).isAfter(maxDate))
                    maxDate = start.clone().add(this.dateLimit);
                if (maxDate && end.isAfter(maxDate))
                    end = maxDate.clone();

                // If the end of the range is before the minimum or the start of the range is
                // after the maximum, don't display this range option at all.
                if ((this.minDate && end.isBefore(this.minDate, this.timepicker ? 'minute' : 'day')) 
                  || (maxDate && start.isAfter(maxDate, this.timepicker ? 'minute' : 'day')))
                    continue;

                //Support unicode chars in the range names.
                var elem = document.createElement('textarea');
                elem.innerHTML = range;
                var rangeHtml = elem.value;

                this.ranges[rangeHtml] = [start, end];
            }

            var list = '<ul>';
            for (range in this.ranges) {
                list += '<li data-range-key="' + range + '">' + range + '</li>';
            }
            list += '<li data-range-key="' + this.locale.customRangeLabel + '">' + this.locale.customRangeLabel + '</li>';
            list += '</ul>';
            this.container.find('.ranges').prepend(list);
        }

        if (typeof cb === 'function') {
            this.callback = cb;
        }

        if (!this.timePicker) {
            this.startDate = this.startDate.startOf('day');
            this.endDate = this.endDate.endOf('day');
            this.container.find('.calendar-time').hide();
        }

        //can't be used together for now
        if (this.timePicker && this.autoApply)
            this.autoApply = false;

        if (this.autoApply && typeof options.ranges !== 'object') {
            this.container.find('.ranges').hide();
        } else if (this.autoApply) {
            this.container.find('.applyBtn, .cancelBtn').addClass('hide');
        }

        if (this.singleDatePicker) {
            this.container.addClass('single');
            this.container.find('.calendar.left').addClass('single');
            this.container.find('.calendar.left').show();
            this.container.find('.calendar.right').hide();
            this.container.find('.daterangepicker_input input, .daterangepicker_input > i').hide();
            if (!this.timePicker) {
                this.container.find('.ranges').hide();
            }
        }

        if ((typeof options.ranges === 'undefined' && !this.singleDatePicker) || this.alwaysShowCalendars) {
            this.container.addClass('show-calendar');
        }

        this.container.addClass('opens' + this.opens);

        //swap the position of the predefined ranges if opens right
        if (typeof options.ranges !== 'undefined' && this.opens == 'right') {
            this.container.find('.ranges').prependTo( this.container.find('.calendar.left').parent() );
        }

        //apply CSS classes and labels to buttons
        this.container.find('.applyBtn, .cancelBtn').addClass(this.buttonClasses);
        if (this.applyClass.length)
            this.container.find('.applyBtn').addClass(this.applyClass);
        if (this.cancelClass.length)
            this.container.find('.cancelBtn').addClass(this.cancelClass);
        this.container.find('.applyBtn').html(this.locale.applyLabel);
        this.container.find('.cancelBtn').html(this.locale.cancelLabel);

        //
        // event listeners
        //

        this.container.find('.calendar')
            .on('click.daterangepicker', '.prev', $.proxy(this.clickPrev, this))
            .on('click.daterangepicker', '.next', $.proxy(this.clickNext, this))
            .on('click.daterangepicker', 'td.available', $.proxy(this.clickDate, this))
            .on('mouseenter.daterangepicker', 'td.available', $.proxy(this.hoverDate, this))
            .on('mouseleave.daterangepicker', 'td.available', $.proxy(this.updateFormInputs, this))
            .on('change.daterangepicker', 'select.yearselect', $.proxy(this.monthOrYearChanged, this))
            .on('change.daterangepicker', 'select.monthselect', $.proxy(this.monthOrYearChanged, this))
            .on('change.daterangepicker', 'select.hourselect,select.minuteselect,select.secondselect,select.ampmselect', $.proxy(this.timeChanged, this))
            .on('click.daterangepicker', '.daterangepicker_input input', $.proxy(this.showCalendars, this))
            .on('focus.daterangepicker', '.daterangepicker_input input', $.proxy(this.formInputsFocused, this))
            .on('change.daterangepicker', '.daterangepicker_input input', $.proxy(this.formInputsChanged, this));

        this.container.find('.ranges')
            .on('click.daterangepicker', 'button.applyBtn', $.proxy(this.clickApply, this))
            .on('click.daterangepicker', 'button.cancelBtn', $.proxy(this.clickCancel, this))
            .on('click.daterangepicker', 'li', $.proxy(this.clickRange, this))
            .on('mouseenter.daterangepicker', 'li', $.proxy(this.hoverRange, this))
            .on('mouseleave.daterangepicker', 'li', $.proxy(this.updateFormInputs, this));

        if (this.element.is('input') || this.element.is('button')) {
            this.element.on({
                'click.daterangepicker': $.proxy(this.show, this),
                'focus.daterangepicker': $.proxy(this.show, this),
                'keyup.daterangepicker': $.proxy(this.elementChanged, this),
                'keydown.daterangepicker': $.proxy(this.keydown, this)
            });
        } else {
            this.element.on('click.daterangepicker', $.proxy(this.toggle, this));
        }

        //
        // if attached to a text input, set the initial value
        //

        if (this.element.is('input') && !this.singleDatePicker && this.autoUpdateInput) {
            this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
            this.element.trigger('change');
        } else if (this.element.is('input') && this.autoUpdateInput) {
            this.element.val(this.startDate.format(this.locale.format));
            this.element.trigger('change');
        }

    };

    DateRangePicker.prototype = {

        constructor: DateRangePicker,

        setStartDate: function(startDate) {
            if (typeof startDate === 'string')
                this.startDate = moment(startDate, this.locale.format);

            if (typeof startDate === 'object')
                this.startDate = moment(startDate);

            if (!this.timePicker)
                this.startDate = this.startDate.startOf('day');

            if (this.timePicker && this.timePickerIncrement)
                this.startDate.minute(Math.round(this.startDate.minute() / this.timePickerIncrement) * this.timePickerIncrement);

            if (this.minDate && this.startDate.isBefore(this.minDate)) {
                this.startDate = this.minDate;
                if (this.timePicker && this.timePickerIncrement)
                    this.startDate.minute(Math.round(this.startDate.minute() / this.timePickerIncrement) * this.timePickerIncrement);
            }

            if (this.maxDate && this.startDate.isAfter(this.maxDate)) {
                this.startDate = this.maxDate;
                if (this.timePicker && this.timePickerIncrement)
                    this.startDate.minute(Math.floor(this.startDate.minute() / this.timePickerIncrement) * this.timePickerIncrement);
            }

            if (!this.isShowing)
                this.updateElement();

            this.updateMonthsInView();
        },

        setEndDate: function(endDate) {
            if (typeof endDate === 'string')
                this.endDate = moment(endDate, this.locale.format);

            if (typeof endDate === 'object')
                this.endDate = moment(endDate);

            if (!this.timePicker)
                this.endDate = this.endDate.endOf('day');

            if (this.timePicker && this.timePickerIncrement)
                this.endDate.minute(Math.round(this.endDate.minute() / this.timePickerIncrement) * this.timePickerIncrement);

            if (this.endDate.isBefore(this.startDate))
                this.endDate = this.startDate.clone();

            if (this.maxDate && this.endDate.isAfter(this.maxDate))
                this.endDate = this.maxDate;

            if (this.dateLimit && this.startDate.clone().add(this.dateLimit).isBefore(this.endDate))
                this.endDate = this.startDate.clone().add(this.dateLimit);

            this.previousRightTime = this.endDate.clone();

            if (!this.isShowing)
                this.updateElement();

            this.updateMonthsInView();
        },

        isInvalidDate: function() {
            return false;
        },

        isCustomDate: function() {
            return false;
        },

        updateView: function() {
            if (this.timePicker) {
                this.renderTimePicker('left');
                this.renderTimePicker('right');
                if (!this.endDate) {
                    this.container.find('.right .calendar-time select').attr('disabled', 'disabled').addClass('disabled');
                } else {
                    this.container.find('.right .calendar-time select').removeAttr('disabled').removeClass('disabled');
                }
            }
            if (this.endDate) {
                this.container.find('input[name="daterangepicker_end"]').removeClass('active');
                this.container.find('input[name="daterangepicker_start"]').addClass('active');
            } else {
                this.container.find('input[name="daterangepicker_end"]').addClass('active');
                this.container.find('input[name="daterangepicker_start"]').removeClass('active');
            }
            this.updateMonthsInView();
            this.updateCalendars();
            this.updateFormInputs();
        },

        updateMonthsInView: function() {
            if (this.endDate) {

                //if both dates are visible already, do nothing
                if (!this.singleDatePicker && this.leftCalendar.month && this.rightCalendar.month &&
                    (this.startDate.format('YYYY-MM') == this.leftCalendar.month.format('YYYY-MM') || this.startDate.format('YYYY-MM') == this.rightCalendar.month.format('YYYY-MM'))
                    &&
                    (this.endDate.format('YYYY-MM') == this.leftCalendar.month.format('YYYY-MM') || this.endDate.format('YYYY-MM') == this.rightCalendar.month.format('YYYY-MM'))
                    ) {
                    return;
                }

                this.leftCalendar.month = this.startDate.clone().date(2);
                if (!this.linkedCalendars && (this.endDate.month() != this.startDate.month() || this.endDate.year() != this.startDate.year())) {
                    this.rightCalendar.month = this.endDate.clone().date(2);
                } else {
                    this.rightCalendar.month = this.startDate.clone().date(2).add(1, 'month');
                }

            } else {
                if (this.leftCalendar.month.format('YYYY-MM') != this.startDate.format('YYYY-MM') && this.rightCalendar.month.format('YYYY-MM') != this.startDate.format('YYYY-MM')) {
                    this.leftCalendar.month = this.startDate.clone().date(2);
                    this.rightCalendar.month = this.startDate.clone().date(2).add(1, 'month');
                }
            }
            if (this.maxDate && this.linkedCalendars && !this.singleDatePicker && this.rightCalendar.month > this.maxDate) {
              this.rightCalendar.month = this.maxDate.clone().date(2);
              this.leftCalendar.month = this.maxDate.clone().date(2).subtract(1, 'month');
            }
        },

        updateCalendars: function() {

            if (this.timePicker) {
                var hour, minute, second;
                if (this.endDate) {
                    hour = parseInt(this.container.find('.left .hourselect').val(), 10);
                    minute = parseInt(this.container.find('.left .minuteselect').val(), 10);
                    second = this.timePickerSeconds ? parseInt(this.container.find('.left .secondselect').val(), 10) : 0;
                    if (!this.timePicker24Hour) {
                        var ampm = this.container.find('.left .ampmselect').val();
                        if (ampm === 'PM' && hour < 12)
                            hour += 12;
                        if (ampm === 'AM' && hour === 12)
                            hour = 0;
                    }
                } else {
                    hour = parseInt(this.container.find('.right .hourselect').val(), 10);
                    minute = parseInt(this.container.find('.right .minuteselect').val(), 10);
                    second = this.timePickerSeconds ? parseInt(this.container.find('.right .secondselect').val(), 10) : 0;
                    if (!this.timePicker24Hour) {
                        var ampm = this.container.find('.right .ampmselect').val();
                        if (ampm === 'PM' && hour < 12)
                            hour += 12;
                        if (ampm === 'AM' && hour === 12)
                            hour = 0;
                    }
                }
                this.leftCalendar.month.hour(hour).minute(minute).second(second);
                this.rightCalendar.month.hour(hour).minute(minute).second(second);
            }

            this.renderCalendar('left');
            this.renderCalendar('right');

            //highlight any predefined range matching the current start and end dates
            this.container.find('.ranges li').removeClass('active');
            if (this.endDate == null) return;

            this.calculateChosenLabel();
        },

        renderCalendar: function(side) {

            //
            // Build the matrix of dates that will populate the calendar
            //

            var calendar = side == 'left' ? this.leftCalendar : this.rightCalendar;
            var month = calendar.month.month();
            var year = calendar.month.year();
            var hour = calendar.month.hour();
            var minute = calendar.month.minute();
            var second = calendar.month.second();
            var daysInMonth = moment([year, month]).daysInMonth();
            var firstDay = moment([year, month, 1]);
            var lastDay = moment([year, month, daysInMonth]);
            var lastMonth = moment(firstDay).subtract(1, 'month').month();
            var lastYear = moment(firstDay).subtract(1, 'month').year();
            var daysInLastMonth = moment([lastYear, lastMonth]).daysInMonth();
            var dayOfWeek = firstDay.day();

            //initialize a 6 rows x 7 columns array for the calendar
            var calendar = [];
            calendar.firstDay = firstDay;
            calendar.lastDay = lastDay;

            for (var i = 0; i < 6; i++) {
                calendar[i] = [];
            }

            //populate the calendar with date objects
            var startDay = daysInLastMonth - dayOfWeek + this.locale.firstDay + 1;
            if (startDay > daysInLastMonth)
                startDay -= 7;

            if (dayOfWeek == this.locale.firstDay)
                startDay = daysInLastMonth - 6;

            var curDate = moment([lastYear, lastMonth, startDay, 12, minute, second]);

            var col, row;
            for (var i = 0, col = 0, row = 0; i < 42; i++, col++, curDate = moment(curDate).add(24, 'hour')) {
                if (i > 0 && col % 7 === 0) {
                    col = 0;
                    row++;
                }
                calendar[row][col] = curDate.clone().hour(hour).minute(minute).second(second);
                curDate.hour(12);

                if (this.minDate && calendar[row][col].format('YYYY-MM-DD') == this.minDate.format('YYYY-MM-DD') && calendar[row][col].isBefore(this.minDate) && side == 'left') {
                    calendar[row][col] = this.minDate.clone();
                }

                if (this.maxDate && calendar[row][col].format('YYYY-MM-DD') == this.maxDate.format('YYYY-MM-DD') && calendar[row][col].isAfter(this.maxDate) && side == 'right') {
                    calendar[row][col] = this.maxDate.clone();
                }

            }

            //make the calendar object available to hoverDate/clickDate
            if (side == 'left') {
                this.leftCalendar.calendar = calendar;
            } else {
                this.rightCalendar.calendar = calendar;
            }

            //
            // Display the calendar
            //

            var minDate = side == 'left' ? this.minDate : this.startDate;
            var maxDate = this.maxDate;
            var selected = side == 'left' ? this.startDate : this.endDate;
            var arrow = this.locale.direction == 'ltr' ? {left: 'chevron-left', right: 'chevron-right'} : {left: 'chevron-right', right: 'chevron-left'};

            var html = '<table class="table-condensed">';
            html += '<thead>';
            html += '<tr>';

            // add empty cell for week number
            if (this.showWeekNumbers || this.showISOWeekNumbers)
                html += '<th></th>';

            if ((!minDate || minDate.isBefore(calendar.firstDay)) && (!this.linkedCalendars || side == 'left')) {
                html += '<th class="prev available"><i><</i></th>';
            } else {
                html += '<th></th>';
            }

            var dateHtml = this.locale.monthNames[calendar[1][1].month()] + calendar[1][1].format(" YYYY");

            if (this.showDropdowns) {
                var currentMonth = calendar[1][1].month();
                var currentYear = calendar[1][1].year();
                var maxYear = (maxDate && maxDate.year()) || (currentYear + 5);
                var minYear = (minDate && minDate.year()) || (currentYear - 50);
                var inMinYear = currentYear == minYear;
                var inMaxYear = currentYear == maxYear;

                var monthHtml = '<select class="monthselect">';
                for (var m = 0; m < 12; m++) {
                    if ((!inMinYear || m >= minDate.month()) && (!inMaxYear || m <= maxDate.month())) {
                        monthHtml += "<option value='" + m + "'" +
                            (m === currentMonth ? " selected='selected'" : "") +
                            ">" + this.locale.monthNames[m] + "</option>";
                    } else {
                        monthHtml += "<option value='" + m + "'" +
                            (m === currentMonth ? " selected='selected'" : "") +
                            " disabled='disabled'>" + this.locale.monthNames[m] + "</option>";
                    }
                }
                monthHtml += "</select>";

                var yearHtml = '<select class="yearselect">';
                for (var y = minYear; y <= maxYear; y++) {
                    yearHtml += '<option value="' + y + '"' +
                        (y === currentYear ? ' selected="selected"' : '') +
                        '>' + y + '</option>';
                }
                yearHtml += '</select>';

                dateHtml = monthHtml + yearHtml;
            }

            html += '<th colspan="5" class="month">' + dateHtml + '</th>';
            if ((!maxDate || maxDate.isAfter(calendar.lastDay)) && (!this.linkedCalendars || side == 'right' || this.singleDatePicker)) {
                html += '<th class="next available"><i>> </i></th>';
            } else {
                html += '<th></th>';
            }

            html += '</tr>';
            html += '<tr>';

            // add week number label
            if (this.showWeekNumbers || this.showISOWeekNumbers)
                html += '<th class="week">' + this.locale.weekLabel + '</th>';

            $.each(this.locale.daysOfWeek, function(index, dayOfWeek) {
                html += '<th>' + dayOfWeek + '</th>';
            });

            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            //adjust maxDate to reflect the dateLimit setting in order to
            //grey out end dates beyond the dateLimit
            if (this.endDate == null && this.dateLimit) {
                var maxLimit = this.startDate.clone().add(this.dateLimit).endOf('day');
                if (!maxDate || maxLimit.isBefore(maxDate)) {
                    maxDate = maxLimit;
                }
            }

            for (var row = 0; row < 6; row++) {
                html += '<tr>';

                // add week number
                if (this.showWeekNumbers)
                    html += '<td class="week">' + calendar[row][0].week() + '</td>';
                else if (this.showISOWeekNumbers)
                    html += '<td class="week">' + calendar[row][0].isoWeek() + '</td>';

                for (var col = 0; col < 7; col++) {

                    var classes = [];

                    //highlight today's date
                    if (calendar[row][col].isSame(new Date(), "day"))
                        classes.push('today');

                    //highlight weekends
                    if (calendar[row][col].isoWeekday() > 5)
                        classes.push('weekend');

                    //grey out the dates in other months displayed at beginning and end of this calendar
                    if (calendar[row][col].month() != calendar[1][1].month())
                        classes.push('off');

                    //don't allow selection of dates before the minimum date
                    if (this.minDate && calendar[row][col].isBefore(this.minDate, 'day'))
                        classes.push('off', 'disabled');

                    //don't allow selection of dates after the maximum date
                    if (maxDate && calendar[row][col].isAfter(maxDate, 'day'))
                        classes.push('off', 'disabled');

                    //don't allow selection of date if a custom function decides it's invalid
                    if (this.isInvalidDate(calendar[row][col]))
                        classes.push('off', 'disabled');

                    //highlight the currently selected start date
                    if (calendar[row][col].format('YYYY-MM-DD') == this.startDate.format('YYYY-MM-DD'))
                        classes.push('active', 'start-date');

                    //highlight the currently selected end date
                    if (this.endDate != null && calendar[row][col].format('YYYY-MM-DD') == this.endDate.format('YYYY-MM-DD'))
                        classes.push('active', 'end-date');

                    //highlight dates in-between the selected dates
                    if (this.endDate != null && calendar[row][col] > this.startDate && calendar[row][col] < this.endDate)
                        classes.push('in-range');

                    //apply custom classes for this date
                    var isCustom = this.isCustomDate(calendar[row][col]);
                    if (isCustom !== false) {
                        if (typeof isCustom === 'string')
                            classes.push(isCustom);
                        else
                            Array.prototype.push.apply(classes, isCustom);
                    }

                    var cname = '', disabled = false;
                    for (var i = 0; i < classes.length; i++) {
                        cname += classes[i] + ' ';
                        if (classes[i] == 'disabled')
                            disabled = true;
                    }
                    if (!disabled)
                        cname += 'available';

                    html += '<td class="' + cname.replace(/^\s+|\s+$/g, '') + '" data-title="' + 'r' + row + 'c' + col + '">' + calendar[row][col].date() + '</td>';

                }
                html += '</tr>';
            }

            html += '</tbody>';
            html += '</table>';

            this.container.find('.calendar.' + side + ' .calendar-table').html(html);

        },

        renderTimePicker: function(side) {

            var html, selected, minDate, maxDate = this.maxDate;

            if (this.dateLimit && (!this.maxDate || this.startDate.clone().add(this.dateLimit).isAfter(this.maxDate)))
                maxDate = this.startDate.clone().add(this.dateLimit);

            if (side == 'left') {
                selected = this.startDate.clone();
                minDate = this.minDate;
            } else if (side == 'right') {
                selected = this.endDate ? this.endDate.clone() : this.previousRightTime.clone();
                minDate = this.startDate;

                if (selected.isBefore(this.startDate))
                    selected = this.startDate.clone();

                if (maxDate && selected.isAfter(maxDate))
                    selected = maxDate.clone();

                //Preserve the time already selected
                var timeSelector = this.container.find('.calendar.right .calendar-time div');
                if (!this.endDate && timeSelector.html() != '') {

                    selected.hour(timeSelector.find('.hourselect option:selected').val() || selected.hour());
                    selected.minute(timeSelector.find('.minuteselect option:selected').val() || selected.minute());
                    selected.second(timeSelector.find('.secondselect option:selected').val() || selected.second());

                    if (!this.timePicker24Hour) {
                        var ampm = timeSelector.find('.ampmselect option:selected').val();
                        if (ampm === 'PM' && selected.hour() < 12)
                            selected.hour(selected.hour() + 12);
                        if (ampm === 'AM' && selected.hour() === 12)
                            selected.hour(0);
                    }

                }
            }

            //
            // hours
            //

            html = '<select class="hourselect">';

            var start = this.timePicker24Hour ? 0 : 1;
            var end = this.timePicker24Hour ? 23 : 12;

            for (var i = start; i <= end; i++) {
                var i_in_24 = i;
                if (!this.timePicker24Hour)
                    i_in_24 = selected.hour() >= 12 ? (i == 12 ? 12 : i + 12) : (i == 12 ? 0 : i);

                var time = selected.clone().hour(i_in_24);
                var disabled = false;
                if (minDate && time.minute(59).isBefore(minDate))
                    disabled = true;
                if (maxDate && time.minute(0).isAfter(maxDate))
                    disabled = true;

                if (i_in_24 == selected.hour() && !disabled) {
                    html += '<option value="' + i + '" selected="selected">' + i + '</option>';
                } else if (disabled) {
                    html += '<option value="' + i + '" disabled="disabled" class="disabled">' + i + '</option>';
                } else {
                    html += '<option value="' + i + '">' + i + '</option>';
                }
            }

            html += '</select> ';

            //
            // minutes
            //

            html += ': <select class="minuteselect">';

            for (var i = 0; i < 60; i += this.timePickerIncrement) {
                var padded = i < 10 ? '0' + i : i;
                var time = selected.clone().minute(i);

                var disabled = false;
                if (minDate && time.second(59).isBefore(minDate))
                    disabled = true;
                if (maxDate && time.second(0).isAfter(maxDate))
                    disabled = true;

                if (selected.minute() == i && !disabled) {
                    html += '<option value="' + i + '" selected="selected">' + padded + '</option>';
                } else if (disabled) {
                    html += '<option value="' + i + '" disabled="disabled" class="disabled">' + padded + '</option>';
                } else {
                    html += '<option value="' + i + '">' + padded + '</option>';
                }
            }

            html += '</select> ';

            //
            // seconds
            //

            if (this.timePickerSeconds) {
                html += ': <select class="secondselect">';

                for (var i = 0; i < 60; i++) {
                    var padded = i < 10 ? '0' + i : i;
                    var time = selected.clone().second(i);

                    var disabled = false;
                    if (minDate && time.isBefore(minDate))
                        disabled = true;
                    if (maxDate && time.isAfter(maxDate))
                        disabled = true;

                    if (selected.second() == i && !disabled) {
                        html += '<option value="' + i + '" selected="selected">' + padded + '</option>';
                    } else if (disabled) {
                        html += '<option value="' + i + '" disabled="disabled" class="disabled">' + padded + '</option>';
                    } else {
                        html += '<option value="' + i + '">' + padded + '</option>';
                    }
                }

                html += '</select> ';
            }

            //
            // AM/PM
            //

            if (!this.timePicker24Hour) {
                html += '<select class="ampmselect">';

                var am_html = '';
                var pm_html = '';

                if (minDate && selected.clone().hour(12).minute(0).second(0).isBefore(minDate))
                    am_html = ' disabled="disabled" class="disabled"';

                if (maxDate && selected.clone().hour(0).minute(0).second(0).isAfter(maxDate))
                    pm_html = ' disabled="disabled" class="disabled"';

                if (selected.hour() >= 12) {
                    html += '<option value="AM"' + am_html + '>AM</option><option value="PM" selected="selected"' + pm_html + '>PM</option>';
                } else {
                    html += '<option value="AM" selected="selected"' + am_html + '>AM</option><option value="PM"' + pm_html + '>PM</option>';
                }

                html += '</select>';
            }

            this.container.find('.calendar.' + side + ' .calendar-time div').html(html);

        },

        updateFormInputs: function() {

            //ignore mouse movements while an above-calendar text input has focus
            if (this.container.find('input[name=daterangepicker_start]').is(":focus") || this.container.find('input[name=daterangepicker_end]').is(":focus"))
                return;

            this.container.find('input[name=daterangepicker_start]').val(this.startDate.format(this.locale.format));
            if (this.endDate)
                this.container.find('input[name=daterangepicker_end]').val(this.endDate.format(this.locale.format));

            if (this.singleDatePicker || (this.endDate && (this.startDate.isBefore(this.endDate) || this.startDate.isSame(this.endDate)))) {
                this.container.find('button.applyBtn').removeAttr('disabled');
            } else {
                this.container.find('button.applyBtn').attr('disabled', 'disabled');
            }

        },

        move: function() {
            var parentOffset = { top: 0, left: 0 },
                containerTop;
            var parentRightEdge = $(window).width();
            if (!this.parentEl.is('body')) {
                parentOffset = {
                    top: this.parentEl.offset().top - this.parentEl.scrollTop(),
                    left: this.parentEl.offset().left - this.parentEl.scrollLeft()
                };
                parentRightEdge = this.parentEl[0].clientWidth + this.parentEl.offset().left;
            }

            if (this.drops == 'up')
                containerTop = this.element.offset().top - this.container.outerHeight() - parentOffset.top;
            else
                containerTop = this.element.offset().top + this.element.outerHeight() - parentOffset.top;
            this.container[this.drops == 'up' ? 'addClass' : 'removeClass']('dropup');

            if (this.opens == 'left') {
                this.container.css({
                    top: containerTop,
                    right: parentRightEdge - this.element.offset().left - this.element.outerWidth(),
                    left: 'auto'
                });
                if (this.container.offset().left < 0) {
                    this.container.css({
                        right: 'auto',
                        left: 9
                    });
                }
            } else if (this.opens == 'center') {
                this.container.css({
                    top: containerTop,
                    left: this.element.offset().left - parentOffset.left + this.element.outerWidth() / 2
                            - this.container.outerWidth() / 2,
                    right: 'auto'
                });
                if (this.container.offset().left < 0) {
                    this.container.css({
                        right: 'auto',
                        left: 9
                    });
                }
            } else {
                this.container.css({
                    top: containerTop,
                    left: this.element.offset().left - parentOffset.left,
                    right: 'auto'
                });
                if (this.container.offset().left + this.container.outerWidth() > $(window).width()) {
                    this.container.css({
                        left: 'auto',
                        right: 0
                    });
                }
            }
        },

        show: function(e) {
            if (this.isShowing) return;

            // Create a click proxy that is private to this instance of datepicker, for unbinding
            this._outsideClickProxy = $.proxy(function(e) { this.outsideClick(e); }, this);

            // Bind global datepicker mousedown for hiding and
            $(document)
              .on('mousedown.daterangepicker', this._outsideClickProxy)
              // also support mobile devices
              .on('touchend.daterangepicker', this._outsideClickProxy)
              // also explicitly play nice with Bootstrap dropdowns, which stopPropagation when clicking them
              .on('click.daterangepicker', '[data-toggle=dropdown]', this._outsideClickProxy)
              // and also close when focus changes to outside the picker (eg. tabbing between controls)
              .on('focusin.daterangepicker', this._outsideClickProxy);

            // Reposition the picker if the window is resized while it's open
            $(window).on('resize.daterangepicker', $.proxy(function(e) { this.move(e); }, this));

            this.oldStartDate = this.startDate.clone();
            this.oldEndDate = this.endDate.clone();
            this.previousRightTime = this.endDate.clone();

            this.updateView();
            this.container.show();
            this.move();
            this.element.trigger('show.daterangepicker', this);
            this.isShowing = true;
        },

        hide: function(e) {
            if (!this.isShowing) return;

            //incomplete date selection, revert to last values
            if (!this.endDate) {
                this.startDate = this.oldStartDate.clone();
                this.endDate = this.oldEndDate.clone();
            }

            //if a new date range was selected, invoke the user callback function
            if (!this.startDate.isSame(this.oldStartDate) || !this.endDate.isSame(this.oldEndDate))
                this.callback(this.startDate, this.endDate, this.chosenLabel);

            //if picker is attached to a text input, update it
            this.updateElement();

            $(document).off('.daterangepicker');
            $(window).off('.daterangepicker');
            this.container.hide();
            this.element.trigger('hide.daterangepicker', this);
            this.isShowing = false;
        },

        toggle: function(e) {
            if (this.isShowing) {
                this.hide();
            } else {
                this.show();
            }
        },

        outsideClick: function(e) {
            var target = $(e.target);
            // if the page is clicked anywhere except within the daterangerpicker/button
            // itself then call this.hide()
            if (
                // ie modal dialog fix
                e.type == "focusin" ||
                target.closest(this.element).length ||
                target.closest(this.container).length ||
                target.closest('.calendar-table').length
                ) return;
            this.hide();
        },

        showCalendars: function() {
            this.container.addClass('show-calendar');
            this.move();
            this.element.trigger('showCalendar.daterangepicker', this);
        },

        hideCalendars: function() {
            this.container.removeClass('show-calendar');
            this.element.trigger('hideCalendar.daterangepicker', this);
        },

        hoverRange: function(e) {

            //ignore mouse movements while an above-calendar text input has focus
            if (this.container.find('input[name=daterangepicker_start]').is(":focus") || this.container.find('input[name=daterangepicker_end]').is(":focus"))
                return;

            var label = e.target.getAttribute('data-range-key');

            if (label == this.locale.customRangeLabel) {
                this.updateView();
            } else {
                var dates = this.ranges[label];
                this.container.find('input[name=daterangepicker_start]').val(dates[0].format(this.locale.format));
                this.container.find('input[name=daterangepicker_end]').val(dates[1].format(this.locale.format));
            }

        },

        clickRange: function(e) {
            var label = e.target.getAttribute('data-range-key');
            this.chosenLabel = label;
            if (label == this.locale.customRangeLabel) {
                this.showCalendars();
            } else {
                var dates = this.ranges[label];
                this.startDate = dates[0];
                this.endDate = dates[1];

                if (!this.timePicker) {
                    this.startDate.startOf('day');
                    this.endDate.endOf('day');
                }

                if (!this.alwaysShowCalendars)
                    this.hideCalendars();
                this.clickApply();
            }
        },

        clickPrev: function(e) {
            var cal = $(e.target).parents('.calendar');
            if (cal.hasClass('left')) {
                this.leftCalendar.month.subtract(1, 'month');
                if (this.linkedCalendars)
                    this.rightCalendar.month.subtract(1, 'month');
            } else {
                this.rightCalendar.month.subtract(1, 'month');
            }
            this.updateCalendars();
        },

        clickNext: function(e) {
            var cal = $(e.target).parents('.calendar');
            if (cal.hasClass('left')) {
                this.leftCalendar.month.add(1, 'month');
            } else {
                this.rightCalendar.month.add(1, 'month');
                if (this.linkedCalendars)
                    this.leftCalendar.month.add(1, 'month');
            }
            this.updateCalendars();
        },

        hoverDate: function(e) {

            //ignore mouse movements while an above-calendar text input has focus
            //if (this.container.find('input[name=daterangepicker_start]').is(":focus") || this.container.find('input[name=daterangepicker_end]').is(":focus"))
            //    return;

            //ignore dates that can't be selected
            if (!$(e.target).hasClass('available')) return;

            //have the text inputs above calendars reflect the date being hovered over
            var title = $(e.target).attr('data-title');
            var row = title.substr(1, 1);
            var col = title.substr(3, 1);
            var cal = $(e.target).parents('.calendar');
            var date = cal.hasClass('left') ? this.leftCalendar.calendar[row][col] : this.rightCalendar.calendar[row][col];

            if (this.endDate && !this.container.find('input[name=daterangepicker_start]').is(":focus")) {
                this.container.find('input[name=daterangepicker_start]').val(date.format(this.locale.format));
            } else if (!this.endDate && !this.container.find('input[name=daterangepicker_end]').is(":focus")) {
                this.container.find('input[name=daterangepicker_end]').val(date.format(this.locale.format));
            }

            //highlight the dates between the start date and the date being hovered as a potential end date
            var leftCalendar = this.leftCalendar;
            var rightCalendar = this.rightCalendar;
            var startDate = this.startDate;
            if (!this.endDate) {
                this.container.find('.calendar td').each(function(index, el) {

                    //skip week numbers, only look at dates
                    if ($(el).hasClass('week')) return;

                    var title = $(el).attr('data-title');
                    var row = title.substr(1, 1);
                    var col = title.substr(3, 1);
                    var cal = $(el).parents('.calendar');
                    var dt = cal.hasClass('left') ? leftCalendar.calendar[row][col] : rightCalendar.calendar[row][col];

                    if ((dt.isAfter(startDate) && dt.isBefore(date)) || dt.isSame(date, 'day')) {
                        $(el).addClass('in-range');
                    } else {
                        $(el).removeClass('in-range');
                    }

                });
            }

        },

        clickDate: function(e) {

            if (!$(e.target).hasClass('available')) return;

            var title = $(e.target).attr('data-title');
            var row = title.substr(1, 1);
            var col = title.substr(3, 1);
            var cal = $(e.target).parents('.calendar');
            var date = cal.hasClass('left') ? this.leftCalendar.calendar[row][col] : this.rightCalendar.calendar[row][col];

            //
            // this function needs to do a few things:
            // * alternate between selecting a start and end date for the range,
            // * if the time picker is enabled, apply the hour/minute/second from the select boxes to the clicked date
            // * if autoapply is enabled, and an end date was chosen, apply the selection
            // * if single date picker mode, and time picker isn't enabled, apply the selection immediately
            //

            if (this.endDate || date.isBefore(this.startDate, 'day')) { //picking start
                if (this.timePicker) {
                    var hour = parseInt(this.container.find('.left .hourselect').val(), 10);
                    if (!this.timePicker24Hour) {
                        var ampm = this.container.find('.left .ampmselect').val();
                        if (ampm === 'PM' && hour < 12)
                            hour += 12;
                        if (ampm === 'AM' && hour === 12)
                            hour = 0;
                    }
                    var minute = parseInt(this.container.find('.left .minuteselect').val(), 10);
                    var second = this.timePickerSeconds ? parseInt(this.container.find('.left .secondselect').val(), 10) : 0;
                    date = date.clone().hour(hour).minute(minute).second(second);
                }
                this.endDate = null;
                this.setStartDate(date.clone());
            } else if (!this.endDate && date.isBefore(this.startDate)) {
                //special case: clicking the same date for start/end,
                //but the time of the end date is before the start date
                this.setEndDate(this.startDate.clone());
            } else { // picking end
                if (this.timePicker) {
                    var hour = parseInt(this.container.find('.right .hourselect').val(), 10);
                    if (!this.timePicker24Hour) {
                        var ampm = this.container.find('.right .ampmselect').val();
                        if (ampm === 'PM' && hour < 12)
                            hour += 12;
                        if (ampm === 'AM' && hour === 12)
                            hour = 0;
                    }
                    var minute = parseInt(this.container.find('.right .minuteselect').val(), 10);
                    var second = this.timePickerSeconds ? parseInt(this.container.find('.right .secondselect').val(), 10) : 0;
                    date = date.clone().hour(hour).minute(minute).second(second);
                }
                this.setEndDate(date.clone());
                if (this.autoApply) {
                  this.calculateChosenLabel();
                  this.clickApply();
                }
            }

            if (this.singleDatePicker) {
                this.setEndDate(this.startDate);
                if (!this.timePicker)
                    this.clickApply();
            }

            this.updateView();

        },

        calculateChosenLabel: function() {
          var customRange = true;
          var i = 0;
          for (var range in this.ranges) {
              if (this.timePicker) {
                  if (this.startDate.isSame(this.ranges[range][0]) && this.endDate.isSame(this.ranges[range][1])) {
                      customRange = false;
                      this.chosenLabel = this.container.find('.ranges li:eq(' + i + ')').addClass('active').html();
                      break;
                  }
              } else {
                  //ignore times when comparing dates if time picker is not enabled
                  if (this.startDate.format('YYYY-MM-DD') == this.ranges[range][0].format('YYYY-MM-DD') && this.endDate.format('YYYY-MM-DD') == this.ranges[range][1].format('YYYY-MM-DD')) {
                      customRange = false;
                      this.chosenLabel = this.container.find('.ranges li:eq(' + i + ')').addClass('active').html();
                      break;
                  }
              }
              i++;
          }
          if (customRange) {
              this.chosenLabel = this.container.find('.ranges li:last').addClass('active').html();
              this.showCalendars();
          }
        },

        clickApply: function(e) {
            this.hide();
            this.element.trigger('apply.daterangepicker', this);
        },

        clickCancel: function(e) {
            this.startDate = this.oldStartDate;
            this.endDate = this.oldEndDate;
            this.hide();
            this.element.trigger('cancel.daterangepicker', this);
        },

        monthOrYearChanged: function(e) {
            var isLeft = $(e.target).closest('.calendar').hasClass('left'),
                leftOrRight = isLeft ? 'left' : 'right',
                cal = this.container.find('.calendar.'+leftOrRight);

            // Month must be Number for new moment versions
            var month = parseInt(cal.find('.monthselect').val(), 10);
            var year = cal.find('.yearselect').val();

            if (!isLeft) {
                if (year < this.startDate.year() || (year == this.startDate.year() && month < this.startDate.month())) {
                    month = this.startDate.month();
                    year = this.startDate.year();
                }
            }

            if (this.minDate) {
                if (year < this.minDate.year() || (year == this.minDate.year() && month < this.minDate.month())) {
                    month = this.minDate.month();
                    year = this.minDate.year();
                }
            }

            if (this.maxDate) {
                if (year > this.maxDate.year() || (year == this.maxDate.year() && month > this.maxDate.month())) {
                    month = this.maxDate.month();
                    year = this.maxDate.year();
                }
            }

            if (isLeft) {
                this.leftCalendar.month.month(month).year(year);
                if (this.linkedCalendars)
                    this.rightCalendar.month = this.leftCalendar.month.clone().add(1, 'month');
            } else {
                this.rightCalendar.month.month(month).year(year);
                if (this.linkedCalendars)
                    this.leftCalendar.month = this.rightCalendar.month.clone().subtract(1, 'month');
            }
            this.updateCalendars();
        },

        timeChanged: function(e) {

            var cal = $(e.target).closest('.calendar'),
                isLeft = cal.hasClass('left');

            var hour = parseInt(cal.find('.hourselect').val(), 10);
            var minute = parseInt(cal.find('.minuteselect').val(), 10);
            var second = this.timePickerSeconds ? parseInt(cal.find('.secondselect').val(), 10) : 0;

            if (!this.timePicker24Hour) {
                var ampm = cal.find('.ampmselect').val();
                if (ampm === 'PM' && hour < 12)
                    hour += 12;
                if (ampm === 'AM' && hour === 12)
                    hour = 0;
            }

            if (isLeft) {
                var start = this.startDate.clone();
                start.hour(hour);
                start.minute(minute);
                start.second(second);
                this.setStartDate(start);
                if (this.singleDatePicker) {
                    this.endDate = this.startDate.clone();
                } else if (this.endDate && this.endDate.format('YYYY-MM-DD') == start.format('YYYY-MM-DD') && this.endDate.isBefore(start)) {
                    this.setEndDate(start.clone());
                }
            } else if (this.endDate) {
                var end = this.endDate.clone();
                end.hour(hour);
                end.minute(minute);
                end.second(second);
                this.setEndDate(end);
            }

            //update the calendars so all clickable dates reflect the new time component
            this.updateCalendars();

            //update the form inputs above the calendars with the new time
            this.updateFormInputs();

            //re-render the time pickers because changing one selection can affect what's enabled in another
            this.renderTimePicker('left');
            this.renderTimePicker('right');

        },

        formInputsChanged: function(e) {
            var isRight = $(e.target).closest('.calendar').hasClass('right');
            var start = moment(this.container.find('input[name="daterangepicker_start"]').val(), this.locale.format);
            var end = moment(this.container.find('input[name="daterangepicker_end"]').val(), this.locale.format);

            if (start.isValid() && end.isValid()) {

                if (isRight && end.isBefore(start))
                    start = end.clone();

                this.setStartDate(start);
                this.setEndDate(end);

                if (isRight) {
                    this.container.find('input[name="daterangepicker_start"]').val(this.startDate.format(this.locale.format));
                } else {
                    this.container.find('input[name="daterangepicker_end"]').val(this.endDate.format(this.locale.format));
                }

            }

            this.updateView();
        },

        formInputsFocused: function(e) {

            // Highlight the focused input
            this.container.find('input[name="daterangepicker_start"], input[name="daterangepicker_end"]').removeClass('active');
            $(e.target).addClass('active');

            // Set the state such that if the user goes back to using a mouse, 
            // the calendars are aware we're selecting the end of the range, not
            // the start. This allows someone to edit the end of a date range without
            // re-selecting the beginning, by clicking on the end date input then
            // using the calendar.
            var isRight = $(e.target).closest('.calendar').hasClass('right');
            if (isRight) {
                this.endDate = null;
                this.setStartDate(this.startDate.clone());
                this.updateView();
            }

        },

        elementChanged: function() {
            if (!this.element.is('input')) return;
            if (!this.element.val().length) return;
            if (this.element.val().length < this.locale.format.length) return;

            var dateString = this.element.val().split(this.locale.separator),
                start = null,
                end = null;

            if (dateString.length === 2) {
                start = moment(dateString[0], this.locale.format);
                end = moment(dateString[1], this.locale.format);
            }

            if (this.singleDatePicker || start === null || end === null) {
                start = moment(this.element.val(), this.locale.format);
                end = start;
            }

            if (!start.isValid() || !end.isValid()) return;

            this.setStartDate(start);
            this.setEndDate(end);
            this.updateView();
        },

        keydown: function(e) {
            //hide on tab or enter
            if ((e.keyCode === 9) || (e.keyCode === 13)) {
                this.hide();
            }
        },

        updateElement: function() {
            if (this.element.is('input') && !this.singleDatePicker && this.autoUpdateInput) {
                this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
                this.element.trigger('change');
            } else if (this.element.is('input') && this.autoUpdateInput) {
                this.element.val(this.startDate.format(this.locale.format));
                this.element.trigger('change');
            }
        },

        remove: function() {
            this.container.remove();
            this.element.off('.daterangepicker');
            this.element.removeData();
        }

    };

    $.fn.daterangepicker = function(options, callback) {
        this.each(function() {
            var el = $(this);
            if (el.data('daterangepicker'))
                el.data('daterangepicker').remove();
            el.data('daterangepicker', new DateRangePicker(el, options, callback));
        });
        return this;
    };

    return DateRangePicker;

}));




(function(host, name, undefined){
	
	var Main = {};
	Main.list = {};//奖期对象列表
	Main.list.playlist = {};//游戏玩法对象列表
	Main.list.informationlist = {};//信息模板对象列表
	
	host[name] = Main;

})(bomao, "Lucky28");









(function(host, name, Event, undefined){
	var defConfig = {
		//ID
		id : -1,
		//游戏名称
		name : '',
		//父类容器
		UIContainer:'',
		//自身游戏容器
		container : '',
		gameMothed:null,
		clock:null,
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.id = cfg.id;
			me.name = cfg.name;
			me._token = '';
			me.prize_group = '';
			me.curPrizeIndex = 0;
			//数据模型
			me.tag_data = [0,-1,-1];
			//游戏赔率数据
			me.gameMothed=cfg.gameMothed;
			//构建游戏模型
			me.UIContainer = $(cfg.UIContainer);
			me.container = $('<div class="game-panel"></div>').appendTo(me.UIContainer);
			me.container.html(html_all.join(''));
			//游戏订单
			me.game_orders = [];
			//游戏初始化
			me.initload = true;
			//时钟
			me.clock = cfg.clock;
			//最大下注限额
			me.bet_max_amount = 0;

			//初始化订单窗口(为每个游戏配置一个下单窗口)
			// me.order_widnow = new bomao.Lucky28.list.orderWindow({'parentGame':me});

			//初始化历史走势
			me.mini_history = new bomao.Lucky28.list.miniHistory({'parentGame':me});

			//数据对象数组
			me.priedIDArr = [];
			//奖期对象数组
			me.caches = [];
			//当前奖期
			me.currentPrize = null;
			//动画中
			me.isAnimating = false;

		},
		getId:function(){
			return this.id;
		},
		//设置ID
		setId:function(id){
			this.id = Number(id);
		},
		getName:function(){
			return this.name;
		},
		//设置游戏名称
		setName:function(name){
			this.name = name;
		},
		//玩法的父类容器
		getPlayContainer:function(){
			var me = this;
			return me.PlayContainer || (me.PlayContainer  = $(me.defConfig.PlayContainer));
		},
		//添加奖期
		addPrize:function(prizeId,leftTime,cycleTime,result_num,entertainedTime){
			var me = this;
			var bet_game  = null;

			if(result_num != ''){
				//奖期号，剩余时间，游戏，奖期状态
				bet_game = new bomao.Lucky28.list.prizePeriod({'prize_id':prizeId,'leftTime':leftTime,'cycleTime':cycleTime,'parentGame':me,'status':4,'entertainedTime':entertainedTime});
				
				var result_data = {
					'num_1':Number(result_num.charAt(0)),
					'num_2':Number(result_num.charAt(1)),
					'num_3':Number(result_num.charAt(2)),
					'num_total':Number(result_num.charAt(0))+Number(result_num.charAt(1))+Number(result_num.charAt(2))
				}
				bet_game.result_number = result_data;
				bet_game.information_result.updateResult(result_data);
			}else{
				//判断是否处于停盘状态
				if(leftTime>cycleTime){
					bet_game = new bomao.Lucky28.list.prizePeriod({'prize_id':prizeId,'leftTime':leftTime,'cycleTime':cycleTime,'parentGame':me,'status':5,'entertainedTime':entertainedTime});
				}else{
					bet_game = new bomao.Lucky28.list.prizePeriod({'prize_id':prizeId,'leftTime':leftTime,'cycleTime':cycleTime,'parentGame':me,'status':0,'entertainedTime':entertainedTime});
				}
			}

			if(me.initload){
				me.currentPrize = bet_game;
			}

			if(me.initload){
				for(var i=0;i<me.game_orders.length;i++){
					if(me.game_orders[i].number == prizeId && me.game_orders[i].status != "已撤销"){
						bet_game.prize_orders.push(me.game_orders[i]);
					}
				}
				//对投注记录进行解析
				bet_game.analyzeOrderRecords();
			}

			//数组保留3期数据
			if(me.priedIDArr.length == 3){
				var del_prize = me.container.find('.bet-history-content').children().eq(0).get(0);
				del_prize.parentNode.removeChild(del_prize);
			}
			if(me.priedIDArr.length > 3){
				me.priedIDArr.pop();
				me.caches.pop();

				var del_prize = me.container.find('.bet-history-content').children().eq(0).get(0);
				del_prize.parentNode.removeChild(del_prize);
			}
			//添加一期奖期数据
			me.priedIDArr.unshift(prizeId);
			me.caches.unshift(bet_game);
			
			bet_game.container.css('display','none');

			me.updataTags(me.tag_data);
		},
		//切换奖期
		switchPrize:function(index){
			var me = this;
			//获取将要展示的对象群组结构
			i=0,
			len = me.tag_data.length;
			
			for(;i<len;i++){
				if(i == index){
					me.tag_data[index] = 0;
				}else{
					me.tag_data[i] = -1;
				}
			}

			if(index != 0){
				me.container.find('.przie-left-time').removeClass('przie-left-time-hide').addClass('przie-left-time-show');

				me.container.find('.play-button').removeClass().addClass('play-button').addClass('play-button-2');

			}else{
				me.container.find('.przie-left-time').removeClass('przie-left-time-show').addClass('przie-left-time-hide');

				me.container.find('.play-button').removeClass().addClass('play-button').addClass('play-button-1');
			}

			me.lastPrize = me.currentPrize;

			me.currentPrize = me.getPrizePeriodByNumber(index);
			me.fireEvent('afert_select_recompense' ,me.tag_data);

			me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).get(0).addEventListener("webkitAnimationStart",function(){
				me.isAnimating = true;
			});

			me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).get(0).addEventListener("animationstart",function(){
				me.isAnimating = true;
			});

			me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).get(0).addEventListener("webkitAnimationEnd",function(){
				me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).removeClass('prize-up-move');
				me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).removeClass('prize-down-move');

				me.container.find('.prize-id-'+me.lastPrize.prize_id).removeClass('prize-up-move-miss');
				me.container.find('.prize-id-'+me.lastPrize.prize_id).removeClass('prize-down-move-miss');

				if(me.lastPrize.prize_id != me.currentPrize.prize_id){
					me.container.find('.prize-id-'+me.lastPrize.prize_id).hide();
				}

				me.isAnimating = false;
			});

			me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).get(0).addEventListener("animationend",function(){
				me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).removeClass('prize-up-move');
				me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).removeClass('prize-down-move');

				me.container.find('.prize-id-'+me.lastPrize.prize_id).removeClass('prize-up-move-miss');
				me.container.find('.prize-id-'+me.lastPrize.prize_id).removeClass('prize-down-move-miss');
				
				if(me.lastPrize.prize_id != me.currentPrize.prize_id){
					me.container.find('.prize-id-'+me.lastPrize.prize_id).hide();
				}

				me.isAnimating = false;
			});
		},
		//自动切换将期
		autoSwitchPrize:function(index){
			var me = this;

			//获取将要展示的对象群组结构
			i=0,
			len = me.tag_data.length;
			
			for(;i<len;i++){
				if(i == index){
					me.tag_data[index] = 0;
				}else{
					me.tag_data[i] = -1;
				}
			}

			if(index != 0){
				me.container.find('.przie-left-time').removeClass('przie-left-time-hide').addClass('przie-left-time-show');

				me.container.find('.play-button').removeClass().addClass('play-button').addClass('play-button-2');

			}else{
				me.container.find('.przie-left-time').removeClass('przie-left-time-show').addClass('przie-left-time-hide');

				me.container.find('.play-button').removeClass().addClass('play-button').addClass('play-button-1');
			}

			me.currentPrize = me.getPrizePeriodByNumber(index);
			me.fireEvent('auto_switch_recompense' ,me.tag_data);
		},
		//获取当前奖期
		getPrizePeriodByNumber:function(index){
			var me = this;
			for(var i in me.caches){
				if(i== index){
					return me.caches[i];
				}
			}
			return me.caches[0] ;
		},
		//获取当前奖期数据
		getPrizePeriodDataByNumber:function(index){
			var me = this;
			for(var i in me.priedIDArr){
				if(i== index){
					return me.priedIDArr[i];
				}
			}
			return me.priedIDArr[0] ;
		},
		//获取当前奖期
		getCurrentPrize:function(){
			var me = this;
			return me.currentPrize;
		},
		//获取当前奖期Dom节点
		getCurrentPrizeDOM:function(index){
			var me = this;
			return me.container.find(".bet-history-panel");
		},
		//显示下注窗口
		// showOrderWindow:function(cell_data){
		// 	var me = this;
		// 	me.container.find('.order-panel').removeClass('order-panel-hide').addClass('order-panel-show');

		// 	me.order_widnow.updateContent(cell_data);
		// },
		//隐藏下注窗口
		// hideOrderWindow:function(){
		// 	var me = this;
		// 	me.container.find('.order-panel').removeClass('order-panel-show').addClass('order-panel-hide');
		// },
		//更新tag标签内容
		updataTags:function(tag_data){
			var me = this;
			var tags = me.container.find('.tag-lab');

			for(var i=0 ; i<3 ; i++){
				if(me.priedIDArr[i]){
					if(i==0){
						tags.eq(i).text('No.'+me.priedIDArr[i]);
					}else{
						tags.eq(i).text('No...'+me.priedIDArr[i].substring(me.priedIDArr[i].length - 3));
					}
				}
			}

			me.showPrizesStatus();
		},
		//显示奖期的状态
		showPrizesStatus:function(){
			var me = this;

			if(me.caches){
				for(var i in me.caches){
					var class_str = '.lab-'+i;
					me.container.find(class_str).html(me.analysisStatus(me.caches[i].status));
				}
			}
		},
		//根据奖期的状态返回文字显示
		analysisStatus:function(prizeStatus){
			var me = this;

			var lab = '';
			switch(prizeStatus){
				case 0 : lab = '受注中';break;
				case 1 : lab = '已封盘';break;
				case 2 : lab = '待开奖';break;
				case 3 : lab = '开奖中';break;
				case 4 : lab = '已开奖';break;
				case 5 : lab = '已停盘';break;
				default : lab = '已取消';break;
			}

			return lab;
		},
		//当前奖期的剩余时间
		showDeadLine:function(endTime){
			var me = this;
			me.leftEndTime = endTime;
			me.container.find('.przie-left-time-lab').html(endTime);
			me.clock.updataLeftTime(me.leftEndTime);
		},
		//通过奖期号获取奖期对象
		getPrizeObjByPrizeId:function(prizeId){
			var me = this;
			for(var i in me.caches){
				if(me.caches[i].prize_id == prizeId){
					return me.caches[i];
				}
			}
		},
		//更新下注信息
		updateBetInformation:function(prizeId , data){
			var me = this;
			me.game_orders = data.data[0].data;

			//根据奖期号获取奖期对象
			var prizeObj = me.getPrizePeriodByNumber(prizeId);
			prizeObj.prize_orders = [];

			for(var i=0;i<me.game_orders.length;i++){
				if(me.game_orders[i].number == prizeId && me.game_orders[i].status != "已撤销"){
					prizeObj.prize_orders.push(me.game_orders[i]);
				}
			}
			
			//对投注记录进行解析
			prizeObj.analyzeOrderRecords();

		}
		
	};

	//html模板
	var html_all = [];

	html_all.push('<div class="bet-history-panel">');
		html_all.push('<ul class="bet-history-nav">');
			html_all.push('<li class="current-recompense recompense-selected" data-param="0">');
				html_all.push('<span class="tag-lab"></span>');
				html_all.push('<span class="prize-status-lab lab-0"></span>');
				html_all.push('<span class="przie-left-time przie-left-time-hide">[<span class="przie-left-time-lab"></span>s]</span>');
			html_all.push('</li>');


			html_all.push('<li class="history-recompense" data-param="1">');
				html_all.push('<span class="tag-lab"></span>');
				html_all.push('<span class="prize-status-lab lab-1"></span>');
			html_all.push('</li>');


			html_all.push('<li class="history-recompense his-r-2" data-param="2">');
				html_all.push('<span class="tag-lab"></span>');
				html_all.push('<span class="prize-status-lab lab-2"></span>');
			html_all.push('</li>');
		html_all.push('</ul>');

		html_all.push('<ul class="play-choose play-choose-select-0">');
			html_all.push('<li class="play-button play-button-1" data-param="0"></li>');
			html_all.push('<li class="play-button play-button-1" data-param="1"></li>');
		html_all.push('</ul>');

		html_all.push('<div class="odds-explain-list odds-list-normal">');
			html_all.push('<ul class="odds-explain-list-menu">');
				html_all.push('<li class="odds-menu">和值</li>');
				html_all.push('<li class="odds-menu">赔率</li>');
				html_all.push('<li class="odds-menu">和值</li>');
				html_all.push('<li class="odds-menu">赔率</li>');
				html_all.push('<li class="odds-menu">和值</li>');
				html_all.push('<li class="odds-menu">赔率</li>');
			html_all.push('</ul>');

			html_all.push('<div class="odds-list-box">');
				html_all.push('<ul class="odds-list-content">');
					$.each([0,1,2,3,4,5,6,7,8,9],function(){
						html_all.push('<li>');
							html_all.push('<span class="odds-content-num">'+this+'</span>');
							html_all.push('<span class="odds-content odds-content-'+this+'"></span>');
						html_all.push('</li>');
					});
				html_all.push('</ul>');

				html_all.push('<ul class="odds-list-content">');
					$.each([10,11,12,13,14,15,16,17,18,19],function(){
						html_all.push('<li>');
							html_all.push('<span class="odds-content-num">'+this+'</span>');
							html_all.push('<span class="odds-content odds-content-'+this+'"></span>');
						html_all.push('</li>');
					});
				html_all.push('</ul>');

				html_all.push('<ul class="odds-list-content">');
					$.each([20,21,22,23,24,25,26,27],function(){
						html_all.push('<li>');
							html_all.push('<span class="odds-content-num">'+this+'</span>');
							html_all.push('<span class="odds-content odds-content-'+this+'"></span>');
						html_all.push('</li>');
					});
				html_all.push('</ul>');
			html_all.push('</div>');
		html_all.push('</div>');

		html_all.push('<div class="bet-history-content">');
		html_all.push('</div>');
	html_all.push('</div>');

	html_all.push('<div class="trend-panel"></div>');




	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;

	host.Lucky28[name] = Main;

})(bomao, "GameBase", bomao.Event);


(function(host, name, parClass, undefined){
	var defConfig = {

	};


	var pros = {
		init:function(cfg){

		}
	};


	var Main = host.Class(pros, parClass);
		Main.defConfig = defConfig;

	host.Lucky28[name] = Main;

})(bomao, "Game", bomao.Lucky28.GameBase);








(function(host, name, Event, undefined){
	var defConfig = {
		name:'DataService',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.gameServerPathHash = {};

		},
		//获取账号余额
		getUserAccount:function(callback){
			var me = this;
			var url = "/users/user-account-info"+'?time='+new Date();
			$.ajax({
				type: "get",
				url: url,
				dataType: "json",
				success: function(data){
					if($.isFunction(callback)){
						callback.call(me , data);
					}
				},
				error:function(data){
					console.log(data);
				}
			 });
		},
		//新增奖期数据
		getGameDataByNumber:function(gameId , callback){
			var me = this;
			var url = '';
			var id = gameId;
			var url = "/bets/load-data/"+gameId+'?time='+new Date();
			$.ajax({
				type: "get",
				url: url,
				dataType: "json",
				success: function(data){
					if($.isFunction(callback)){
						callback.call(me , data['data']);
					}
				},
				error:function(data){
					console.log(data);
				}
			 });
			
		},
		//开奖数据
		getPrizeIssueByPrizeID:function(gameId,callback){
			var me = this;
			var url = "/bets/wnnumber-history/"+gameId+"/1"+'?time='+new Date();
			$.ajax({
				type: "get",
				url: url,
				dataType: "json",
				success: function(data){
					if($.isFunction(callback)){
						callback.call(me , data);
					}
				},
				error:function(data){
					console.log(data);
				}
			 });
		},
		//所有开奖结果数据
		getAllIssueByGameID:function(gameId , callback){
			var me = this;
			var url = "/bets/wnnumber-history/"+gameId+'?time='+new Date();
			$.ajax({
				type: "get",
				url: url,
				dataType: "json",
				success: function(data){
					if($.isFunction(callback)){
						callback.call(me,data);
					}
				},
				error: function(data){
					console.log(data);
				}
			});
		},
		//提交订单
		sumbitOrder:function(gameId,orderData,callback){
			var me = this;
			var url = "/bets/bet/"+gameId;
			var message = new bomao.GameMessage();
			$.ajax({
				type: "post",
				url: url,
				data: orderData,
				dataType: "json",
				beforeSend:function(){
					message.showTip('提交中...');
				},
				success: function(data){
					if(Number(data.isSuccess) != 1){
						message.show(data);
					}
					
					if($.isFunction(callback)){
						callback.call(me , data);
					}
				},
				complete: function(data){
					message.hideTip();
				},
				error:function(data){
					console.log(data);
				}
			});
		},
		//获取订单
		getOrders:function(gameId,callback){
			var me = this;
			var url = "/bets/bet-info/"+gameId+'?time='+new Date();
			$.ajax({
				type: "get",
				url: url,
				dataType: "json",
				success: function(data){
					if($.isFunction(callback)){
						callback.call(me , data);
					}
				},
				error:function(data){
					console.log(data);
				}
			});
		},
		//取消订单
		cancelOrder:function(orderIdArr , issue , lottery_id , token , callback){
			var me=this;
			var url = '/projects/drop-multi-projects';
			var message = new bomao.GameMessage();
			$.ajax({
				type:'post',
				url:url,
				data:{lottery_id:lottery_id , issue:issue , _token : token,project_ids : orderIdArr},
				dataType: "json",
				beforeSend:function(){
					message.showTip('撤单中...');
				},
				success: function(data){
					message.show(data);
					if($.isFunction(callback)){
						callback.call(me , data);
					}
				},
				complete: function(data){
					message.hideTip();
				},
				error:function(data){
					console.log(data);
				}
			});
		}
	};

	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;

	host.Lucky28[name] = Main;

})(bomao,"DataService", bomao.Event);
//j奖期类 --对游戏类和信息面板类进行实例化 并加载模块
(function(host, Event, undefined){
	var defConfig = {
		name:'prizePeriod',
		container:'',
		UIContainer:'.bet-history-content',
		//受注\封盘\历史状态
		status:0,
		parentGame:null,
		//剩余时间
		leftTime:0,
		//奖期周期
		cycleTime:0,
		//奖期号
		prize_id:'',
		//封盘时间
		entertainedTime:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.UIContainer = cfg.UIContainer;
			me.status = cfg.status;
			me.prize_id = cfg.prize_id;
			me.leftTime = cfg.leftTime;
			me.parentGame=cfg.parentGame;
			me.cycleTime = cfg.cycleTime;
			//封盘时间设置
			me.entertainedTime = cfg.entertainedTime;
			me.container = $('<div></div>').appendTo(me.parentGame.container.find(me.UIContainer));
			//玩法的数据结构 0组合 1和值
			me.currentPlayIndex = 0;
			//当前玩法
			me.currentPlay=null;
			//玩法列表
			me.playlist=null;
			//信息列表
			me.informationlist=null;
			//玩法id数组
			me.pladIdArray=null;
			//当前奖期的订单数组
			me.prize_orders = [];
			//开奖结果
			me.result_number=null;

			me.resultNumberData=null;
			//奖金组限制额度
			me.limite_extra=[];

			//切换玩法
			me.addEvent('afert_select_play', function(e, data) {	
				me.showplay(data);
			});
			//切换奖期状态
			me.addEvent('change_prize_status' , function(e,data){
				me.updataStatus(data.status);
			});
			//获取结果
			me.addEvent('start_catch_issue_result' , function(e,data){
				me.catchIssue(data);
			});
			//开奖动画
			me.addEvent('start_lottery_animation' , function(e,data){
				me.startAnimation(data);
			});
			//加载模型
			me.buildUI();

			if(me.parentGame.gameMothed){
				me.initGameMethod(me.parentGame.gameMothed);
			}
		},
		//初始化赔率数据
		initGameMethod:function(data){
			var me = this;
			var daxiao_lab = data.getMethodConfigByName("daxiaodans-bsde-daxiao").extra_prize[0];
			var danshuang = data.getMethodConfigByName("daxiaodans-bsde-danshuang").extra_prize[0];
			var liangji = data.getMethodConfigByName("daxiaodans-bsde-liangji").extra_prize[0];
			var chuanguan = [
							data.getMethodConfigByName("daxiaodans-bsde-chuanguan").extra_prize['00'],
							data.getMethodConfigByName("daxiaodans-bsde-chuanguan").extra_prize['01'],
							data.getMethodConfigByName("daxiaodans-bsde-chuanguan").extra_prize['10'],
							data.getMethodConfigByName("daxiaodans-bsde-chuanguan").extra_prize['11']
							];
			
			me.pladIdArray = [
				data.getMethodConfigByName("daxiaodans-bsde-daxiao").id,
				data.getMethodConfigByName("daxiaodans-bsde-danshuang").id,
				data.getMethodConfigByName("daxiaodans-bsde-liangji").id,
				data.getMethodConfigByName("daxiaodans-bsde-chuanguan").id,
				data.getMethodConfigByName("hezhi-hezhi-hezhi").id
				];

			me.container.find('.daxiaodans-bsde-daxiao').html(daxiao_lab.substring(0,daxiao_lab.length-2));
			me.container.find('.daxiaodans-bsde-danshuang').html(danshuang.substring(0,danshuang.length-2));
			me.container.find('.daxiaodans-bsde-liangji').html(liangji.substring(0,liangji.length-2));
			me.container.find('.daxiaodans-bsde-chuanguan-00').html(chuanguan[0].substring(0,chuanguan[0].length-2));
			me.container.find('.daxiaodans-bsde-chuanguan-01').html(chuanguan[1].substring(0,chuanguan[1].length-2));
			me.container.find('.daxiaodans-bsde-chuanguan-10').html(chuanguan[2].substring(0,chuanguan[2].length-2));
			me.container.find('.daxiaodans-bsde-chuanguan-11').html(chuanguan[3].substring(0,chuanguan[3].length-2));

			
			$.each([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27],function(){
				var class_str = ".tip-"+this;
				var class_str2 = ".odds-content-"+this;

				var extra_prize = data.getMethodConfigByName("hezhi-hezhi-hezhi").extra_prize[this];
				var extra_prize_lab = extra_prize.substring(0,extra_prize.length-2);

				me.container.find(class_str).html(extra_prize_lab);
				me.parentGame.container.find(class_str2).html("x"+extra_prize_lab);

				me.limite_extra.push(data.getMethodConfigByName("hezhi-hezhi-hezhi").extra[this]);
			});
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));
			me.updataStatus(me.status);
			//加载玩法模型
			me.loadplay();
			me.loadinformation();

			me.container.find('.bet-panel-hezhi').get(0).addEventListener("webkitAnimationStart",function(){
				me.parentGame.isAnimating = true;
				me.container.find('.bet').addClass('bet-over');
				me.container.find('.bet-panel-hezhi').show();
				me.container.find('.bet-panel-zuhe').show();
			});
			me.container.find('.bet-panel-hezhi').get(0).addEventListener("webkitAnimationEnd",function(){
				me.container.find('.bet-panel-hezhi').removeClass('prize-left-move').removeClass('prize-right-move-miss');
				if(me.currentPlayIndex==0){
					me.container.find('.bet-panel-hezhi').hide();
				}else{
					me.container.find('.bet-panel-hezhi').show();
				}
				me.container.find('.bet').removeClass('bet-over');
				me.parentGame.isAnimating = false;
			});
			me.container.find('.bet-panel-hezhi').get(0).addEventListener("animationstart",function(){
				me.parentGame.isAnimating = true;
				me.container.find('.bet').addClass('bet-over');
				me.container.find('.bet-panel-hezhi').show();
				me.container.find('.bet-panel-zuhe').show();
			});
			me.container.find('.bet-panel-hezhi').get(0).addEventListener("animationend",function(){
				me.container.find('.bet-panel-hezhi').removeClass('prize-left-move').removeClass('prize-right-move-miss');
				if(me.currentPlayIndex==0){
					me.container.find('.bet-panel-hezhi').hide();
				}else{
					me.container.find('.bet-panel-hezhi').show();
				}
				me.container.find('.bet').removeClass('bet-over');
				me.parentGame.isAnimating = false;
			});

			me.container.find('.bet-panel-zuhe').get(0).addEventListener("webkitAnimationStart",function(){
				me.container.find('.bet').addClass('bet-over');
				me.container.find('.bet-panel-hezhi').show();
				me.container.find('.bet-panel-zuhe').show();
			});
			me.container.find('.bet-panel-zuhe').get(0).addEventListener("webkitAnimationEnd",function(){
				me.container.find('.bet-panel-zuhe').removeClass('prize-left-move-miss').removeClass('prize-right-move');
				if(me.currentPlayIndex==0){
					me.container.find('.bet-panel-zuhe').show();
				}else{
					me.container.find('.bet-panel-zuhe').hide();
				}
				me.container.find('.bet').removeClass('bet-over');
			});
			me.container.find('.bet-panel-zuhe').get(0).addEventListener("animationstart",function(){
				me.container.find('.bet').addClass('bet-over');
				me.container.find('.bet-panel-hezhi').show();
				me.container.find('.bet-panel-zuhe').show();
			});
			me.container.find('.bet-panel-zuhe').get(0).addEventListener("animationend",function(){
				me.container.find('.bet-panel-zuhe').removeClass('prize-left-move-miss').removeClass('prize-right-move');
				if(me.currentPlayIndex==0){
					me.container.find('.bet-panel-zuhe').show();
				}else{
					me.container.find('.bet-panel-zuhe').hide();
				}
				me.container.find('.bet').removeClass('bet-over');
			});

		},
		//加载玩法模型
		loadplay:function(){
			var me = this;
			//组合玩法
			me.play_zuhe = new bomao.Lucky28.list.playlist.zuhe({'parentPrize':me});
			me.play_zuhe.container = me.container.find('.bet-panel-zuhe');
			// me.container.find('.bet-panel-zuhe').hide();
			me.play_zuhe.buildUI();
			//和值玩法
			me.play_hezhi = new bomao.Lucky28.list.playlist.hezhi({'parentPrize':me});
			me.play_hezhi.container = me.container.find('.bet-panel-hezhi');
			me.play_hezhi.buildUI();

			me.playlist =[me.play_zuhe,me.play_hezhi];
			me.currentPlay = me.play_zuhe;
		},
		//切换玩法
		swtichplay:function(index){
			var me = this;

			if(index == 1 && me.currentPlayIndex!=1){
				me.container.find('.bet-panel-hezhi').addClass('prize-left-move');
				me.container.find('.bet-panel-zuhe').addClass('prize-left-move-miss');
			}
			if(index == 0 && me.currentPlayIndex!=0){
				me.container.find('.bet-panel-hezhi').addClass('prize-right-move-miss');
				me.container.find('.bet-panel-zuhe').addClass('prize-right-move');
			}

			me.currentPlayIndex = index;
			me.currentPlay = me.playlist[index];
			
			me.fireEvent('afert_select_play',me.currentPlayIndex);
		},
		//展示玩法
		showplay:function(index){
			var me = this;
			//切换选择框
			me.parentGame.container.find('.play-choose').removeClass().addClass('play-choose').addClass('play-choose-select-'+index);
			//切换玩法
			me.parentGame.container.find('.prize-id-'+me.prize_id).find('.bet').removeClass().addClass('bet').addClass('bet-panel-'+index);
		},
		//获取当前奖期的游戏玩法
		getCurrentPlay:function(){
			var me = this;
			return me.currentPlay;
		},
		//获取当前奖期的游戏玩法Dom节点
		getCurrentPlayDOM:function(index){
			var me = this;
			if(index == 0){
				return me.container.find(".bet-panel-zuhe");
			}else{
				return me.container.find(".bet-panel-hezhi");
			}
		},
		//加载信息面板
		loadinformation:function(){
			var me = this;
			//倒计时信息模板
			me.information_timer = new bomao.Lucky28.list.informationlist.informationtimer();
			me.information_timer.parentPrize = me;
			me.information_timer.container = me.container.find('.information-panel-timer');
			me.information_timer.buildUI();

			me.information_timer.setLeftTime(me.leftTime);

			//等待开奖信息模板
			me.information_wait = new bomao.Lucky28.list.informationlist.informationwait();
			me.information_wait.parentPrize = me;
			me.information_wait.container = me.container.find('.information-panel-wait');
			me.information_wait.buildUI();

			//开奖结果信息模板
			me.information_result = new bomao.Lucky28.list.informationlist.informationresult();
			me.information_result.parentPrize = me;
			me.information_result.container = me.container.find('.information-panel-result');
			me.information_result.buildUI();

			//停盘信息版
			me.information_suspension = new bomao.Lucky28.list.informationlist.informationSuspension();
			me.information_suspension.parentPrize = me;
			me.information_suspension.container = me.container.find('.information-panel-suspension');
			me.information_suspension.buildUI();

			informationlist=[me.information_timer , me.information_wait , me.information_result , me.information_suspension];

		},
		//修改状态，重新渲染
		//暂定０为受注游戏，１封盘状态　2待开奖状态 3开奖中状态　4已开奖状态 5已封盘状态
		updataStatus:function(status){
			var me = this;
			me.status = status;

			if(status==2){
				me.fireEvent('start_catch_issue_result' , me.prize_id);
			}

			me.container.removeClass().addClass('prize-id-'+me.prize_id).addClass("panel-main-status-"+status);

			me.parentGame.showPrizesStatus();

		},
		//刷新奖期
		fleshPrize:function(leftTime , status){
			var me = this;
			me.leftTime = leftTime;
			me.information_timer.setLeftTime(me.leftTime);
			me.updataStatus(status);
		},
		//获取开奖号码
		catchIssue:function(data){
			var me = this;

			var service = new bomao.Lucky28.DataService();

			service.getPrizeIssueByPrizeID(me.parentGame.id,function(data){
				if(data[1]){
					var resultData = me.prize_id == data[0].number?data[0].code:(me.prize_id == data[1].number?data[1].code : "");
				}else{
					var resultData = data[0].code;
				}

				if(resultData != ""){
					if(resultData != "/"){
						var result_num = resultData.replace(/\s+/g,"");

						me.status = 3;
						me.play_zuhe.container.find('.li-style').removeClass('locked-button').addClass('locked-button');
						me.play_hezhi.container.find('.bet-num').removeClass('locked-button').addClass('locked-button');
						me.play_zuhe.container.find('.li-style-select-bet').find('.bet-money-lab').removeClass('bet-locked-button').addClass('bet-locked-button');
						me.play_zuhe.container.find('.li-style-select-bet').find('.bet-chip-img').removeClass('bet-chip-locked').addClass('bet-chip-locked');
						me.play_zuhe.container.find('.li-style-select-bet').find('.bet-chip-img-normal').removeClass('bet-chip-locked').addClass('bet-chip-locked');
						me.play_hezhi.container.find('.hezhi-li-style-select-bet').removeClass('bet-locked-button').addClass('bet-locked-button');
						me.fireEvent("change_prize_status" , me);

						var result_data = {
							'num_1':Number(result_num.charAt(0)),
							'num_2':Number(result_num.charAt(1)),
							'num_3':Number(result_num.charAt(2)),
							'num_total':Number(result_num.charAt(0))+Number(result_num.charAt(1))+Number(result_num.charAt(2))
						}
						me.result_number = result_data;

						me.information_result.updateResult(result_data);

						me.fireEvent("start_lottery_animation" , result_data);

						//开奖结束后显示走势图
						me.resultNumberData = {'code':resultData , 'number':me.prize_id};
					}else{
						//取消奖期效果
						me.information_wait.container.find('.wait_label').html("奖期已取消");
						var lab_str = ".lab-"+me.parentGame.priedIDArr.indexOf(me.prize_id);
						me.parentGame.container.find(lab_str).html('已取消');
						me.status = 6;
						//开奖结束后显示走势图
						me.parentGame.mini_history.updataSourceData({'code':resultData,'number':me.prize_id});
					}
					
				}else{
					me.parentGame.mini_history.updataSourceData({'code':'','number':me.prize_id});

					var updata = setInterval(function(){
						service.getPrizeIssueByPrizeID(me.parentGame.id,function(data){
							resultData = me.prize_id == data[0].number?data[0].code:(me.prize_id == data[1].number?data[1].code : "");

							if(resultData != ""){
								clearInterval(updata);
								if(resultData != "/"){
									var result_num = resultData.replace(/\s+/g,"");

									me.status = 3;
									me.play_zuhe.container.find('.li-style').removeClass('locked-button').addClass('locked-button');
									me.play_hezhi.container.find('.bet-num').removeClass('locked-button').addClass('locked-button');
									me.play_zuhe.container.find('.li-style-select-bet').find('.bet-money-lab').removeClass('bet-locked-button').addClass('bet-locked-button');
									me.play_zuhe.container.find('.li-style-select-bet').find('.bet-chip-img').removeClass('bet-chip-locked').addClass('bet-chip-locked');
									me.play_zuhe.container.find('.li-style-select-bet').find('.bet-chip-img-normal').removeClass('bet-chip-locked').addClass('bet-chip-locked');
									me.play_hezhi.container.find('.hezhi-li-style-select-bet').removeClass('bet-locked-button').addClass('bet-locked-button');
									me.fireEvent("change_prize_status" , me);

									var result_data = {
										'num_1':Number(result_num.charAt(0)),
										'num_2':Number(result_num.charAt(1)),
										'num_3':Number(result_num.charAt(2)),
										'num_total':Number(result_num.charAt(0))+Number(result_num.charAt(1))+Number(result_num.charAt(2))
									}

									me.information_result.updateResult(result_data);

									me.fireEvent("start_lottery_animation" , result_data);

									//开奖结束后显示走势图
									me.resultNumberData = {'code':resultData , 'number':me.prize_id};
								}else{
									//取消奖期效果
									me.information_wait.container.find('.wait_label').html("奖期已取消");
									var lab_str = ".lab-"+me.parentGame.priedIDArr.indexOf(me.prize_id);
									me.parentGame.container.find(lab_str).html('已取消');
									me.status = 6;
									//开奖结束后显示走势图
									me.parentGame.mini_history.updataSourceData({'code':resultData,'number':me.prize_id});
								}
							}
						});
					}, 10*1000);
				}
			});
		},
		//开奖动画
		startAnimation:function(result_data){
			var me = this;
			me.information_result.playAnimation(result_data);
		},
		getParentGame:function(){
			var me = this;
			return me.parentGame;
		},
		setParentGame:function(game){
			var me = this;
			me.parentGame = game;
		},
		//解析投注记录
		analyzeOrderRecords:function(){
			var me = this;

			for(var i=0;i<me.play_zuhe.betAmountData.length;i++){
				me.play_zuhe.betAmountData[i] = 0;
			}

			for(var i=0;i<me.play_hezhi.betAmountData.length;i++){
				me.play_hezhi.betAmountData[i] = 0;
			}

			for(var i=0;i<me.prize_orders.length;i++){
				var ball = me.prize_orders[i].balls+'';
				var money = (me.prize_orders[i].money).split('.')[0];

				switch(ball){
					case '大': 
						me.play_zuhe.betAmountData[0] = Number(me.play_zuhe.betAmountData[0])+Number(money);
						break;
					case '小':
						me.play_zuhe.betAmountData[1] = Number(me.play_zuhe.betAmountData[1])+Number(money);
						break;
					case '单':
						me.play_zuhe.betAmountData[2] = Number(me.play_zuhe.betAmountData[2])+Number(money);
						break;
					case '双':
						me.play_zuhe.betAmountData[3] = Number(me.play_zuhe.betAmountData[3])+Number(money);
						break;
					case '极大':
						me.play_zuhe.betAmountData[4] = Number(me.play_zuhe.betAmountData[4])+Number(money);
						break;
					case '极小':
						me.play_zuhe.betAmountData[5] = Number(me.play_zuhe.betAmountData[5])+Number(money);
						break;
					case '大单':
						me.play_zuhe.betAmountData[6] = Number(me.play_zuhe.betAmountData[6])+Number(money);
						break;
					case '大双':
						me.play_zuhe.betAmountData[7] = Number(me.play_zuhe.betAmountData[7])+Number(money);
						break;
					case '小单':
						me.play_zuhe.betAmountData[8] = Number(me.play_zuhe.betAmountData[8])+Number(money);
						break;
					case '小双':
						me.play_zuhe.betAmountData[9] = Number(me.play_zuhe.betAmountData[9])+Number(money);
						break;
					case '0':
						me.play_hezhi.betAmountData[0] = Number(me.play_hezhi.betAmountData[0])+Number(money);
						break;
					case '1':
						me.play_hezhi.betAmountData[1] = Number(me.play_hezhi.betAmountData[1])+Number(money);
						break;
					case '2':
						me.play_hezhi.betAmountData[2] = Number(me.play_hezhi.betAmountData[2])+Number(money);
						break;
					case '3':
						me.play_hezhi.betAmountData[3] = Number(me.play_hezhi.betAmountData[3])+Number(money);
						break;
					case '4':
						me.play_hezhi.betAmountData[4] = Number(me.play_hezhi.betAmountData[4])+Number(money);
						break;
					case '5':
						me.play_hezhi.betAmountData[5] = Number(me.play_hezhi.betAmountData[5])+Number(money);
						break;
					case '6':
						me.play_hezhi.betAmountData[6] = Number(me.play_hezhi.betAmountData[6])+Number(money);
						break;
					case '7':
						me.play_hezhi.betAmountData[7] = Number(me.play_hezhi.betAmountData[7])+Number(money);
						break;
					case '8':
						me.play_hezhi.betAmountData[8] = Number(me.play_hezhi.betAmountData[8])+Number(money);
						break;
					case '9':
						me.play_hezhi.betAmountData[9] = Number(me.play_hezhi.betAmountData[9])+Number(money);
						break;
					case '10':
						me.play_hezhi.betAmountData[10] = Number(me.play_hezhi.betAmountData[10])+Number(money);
						break;
					case '11':
						me.play_hezhi.betAmountData[11] = Number(me.play_hezhi.betAmountData[11])+Number(money);
						break;
					case '12':
						me.play_hezhi.betAmountData[12] = Number(me.play_hezhi.betAmountData[12])+Number(money);
						break;
					case '13':
						me.play_hezhi.betAmountData[13] = Number(me.play_hezhi.betAmountData[13])+Number(money);
						break;
					case '14':
						me.play_hezhi.betAmountData[14] = Number(me.play_hezhi.betAmountData[14])+Number(money);
						break;
					case '15':
						me.play_hezhi.betAmountData[15] = Number(me.play_hezhi.betAmountData[15])+Number(money);
						break;
					case '16':
						me.play_hezhi.betAmountData[16] = Number(me.play_hezhi.betAmountData[16])+Number(money);
						break;
					case '17':
						me.play_hezhi.betAmountData[17] = Number(me.play_hezhi.betAmountData[17])+Number(money);
						break;
					case '18':
						me.play_hezhi.betAmountData[18] = Number(me.play_hezhi.betAmountData[18])+Number(money);
						break;
					case '19':
						me.play_hezhi.betAmountData[19] = Number(me.play_hezhi.betAmountData[19])+Number(money);
						break;
					case '20':
						me.play_hezhi.betAmountData[20] = Number(me.play_hezhi.betAmountData[20])+Number(money);
						break;
					case '21':
						me.play_hezhi.betAmountData[21] = Number(me.play_hezhi.betAmountData[21])+Number(money);
						break;
					case '22':
						me.play_hezhi.betAmountData[22] = Number(me.play_hezhi.betAmountData[22])+Number(money);
						break;
					case '23':
						me.play_hezhi.betAmountData[23] = Number(me.play_hezhi.betAmountData[23])+Number(money);
						break;
					case '24':
						me.play_hezhi.betAmountData[24] = Number(me.play_hezhi.betAmountData[24])+Number(money);
						break;
					case '25':
						me.play_hezhi.betAmountData[25] = Number(me.play_hezhi.betAmountData[25])+Number(money);
						break;
					case '26':
						me.play_hezhi.betAmountData[26] = Number(me.play_hezhi.betAmountData[26])+Number(money);
						break;
					case '27':
						me.play_hezhi.betAmountData[27] = Number(me.play_hezhi.betAmountData[27])+Number(money);
						break;

					default:break;
				}
			}

			me.play_zuhe.updateRealyBetButtonDate();
			me.play_hezhi.updateRealyBetButtonDate();
			if(me.result_number){
				me.information_result.updateResult(me.result_number);
			}
		}
		
	};

	var html_all = [];
		html_all.push('<div class="bet bet-panel-0">');
			html_all.push('<div class="bet-panel bet-panel-zuhe">');
			html_all.push('</div>');

			html_all.push('<div class="bet-panel bet-panel-hezhi">');
			html_all.push('</div>');

			html_all.push('<div class="bet-panel-mask">');
			html_all.push('</div>');
		html_all.push('</div>');

		html_all.push('<div class="information-panel">');
			html_all.push('<div class="information-panel-timer">');
			html_all.push('</div>');

			html_all.push('<div class="information-panel-wait">');
			html_all.push('</div>');

			html_all.push('<div class="information-panel-result">');
			html_all.push('</div>');

			html_all.push('<div class="information-panel-suspension">');
			html_all.push('</div>');
		html_all.push('</div>');

	
	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list[defConfig.name] = Main;
})(bomao, bomao.Event);
(function(host, Event, undefined){
	var defConfig = {
		name:'zuhe',
		parentPrize:null,
		container:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.parentPrize = cfg.parentPrize;
			me.squareData = [-1,-1,-1,-1,-1,-1,-1,-1,-1,-1];
			//已下注金额数组
			me.betAmountData = [0,0,0,0,0,0,0,0,0,0];
			me.current_cell_data = null;

			me.addEvent('after_setSelect', function(e, data){
				me.updateSelect(data);
			});

			me.addEvent('afterReSet', function(){
				me.updateSelect(me.squareData);
			});
		},
		//复位数据
		rebuildData:function(){
			var me = this;
			// 大：1， 小：0，
			// 单：1， 双：0，
			// 极大：1， 极小：0，
			// 大单：11， 大双：10，小单：01， 小双：00 
			//["286", "284", "285", "286", "282"]

			//大、小、单、双、极大、极小、大单、大双、小单、小双
			me.squareData = [-1,-1,-1,-1,-1,-1,-1,-1,-1,-1];
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));

			me.updateRealyBetButtonDate();
		},
		//恢复初始样式
		reSet:function(){
			var me = this;

			for(var i in me.squareData){
				me.squareData[i] = -1;
			}

			me.fireEvent('afterReSet');
		},
		//判断是否处于活跃状态
		isActivity:function(){
			var me = this;

			for(var i in me.squareData){
				if(me.squareData[i] == 0){
					return true;
					break;
				}
			}

			return false;
		},
		//设置选择项
		completeSelect:function(index){
			var me = this;
			i=0,
			len = me.squareData.length;

			var cur_play_id = '';
			var cur_play_type = '';
			var cur_ball = '';
			switch(index){
				case '0' :
					cur_ball = '1';
					cur_play_id = me.parentPrize.pladIdArray[0];
					cur_play_type = "daxiaodans.bsde.daxiao";
					break;
				case '1' : 
					cur_ball = '0';
					cur_play_id = me.parentPrize.pladIdArray[0];
					cur_play_type = "daxiaodans.bsde.daxiao";
					break;
				case '2' :
					cur_ball = '1';
					cur_play_id = me.parentPrize.pladIdArray[1];
					cur_play_type = "daxiaodans.bsde.danshuang";
					break;
				case '3' : 
					cur_ball = '0';
					cur_play_id = me.parentPrize.pladIdArray[1];
					cur_play_type = "daxiaodans.bsde.danshuang";
					break;
				case '4' :
					cur_ball = '1';
					cur_play_id = me.parentPrize.pladIdArray[2];
					cur_play_type = "daxiaodans.bsde.liangji";
					break;
				case '5' : 
					cur_ball = '0';
					cur_play_id = me.parentPrize.pladIdArray[2];
					cur_play_type = "daxiaodans.bsde.liangji";
					break;
				case '6' :
					cur_ball = '11';
					cur_play_id = me.parentPrize.pladIdArray[3];
					cur_play_type = "daxiaodans.bsde.chuanguan";
					break;
				case '7' : 
					cur_ball = '10';
					cur_play_id = me.parentPrize.pladIdArray[3];
					cur_play_type = "daxiaodans.bsde.chuanguan";
					break;
				case '8' :
					cur_ball = '01';
					cur_play_id = me.parentPrize.pladIdArray[3];
					cur_play_type = "daxiaodans.bsde.chuanguan";
					break;
				case '9' : 
					cur_ball = '00';
					cur_play_id = me.parentPrize.pladIdArray[3];
					cur_play_type = "daxiaodans.bsde.chuanguan";
					break;
				default : break;
			}

			for(;i<len;i++){
				if(i==index){
					me.squareData[index] = (me.squareData[index] == 0?-1:0);

					if(me.squareData[index] == 0){
						me.current_cell_data = {
							'city':me.parentPrize.parentGame.name,
							'prize_id':me.parentPrize.prize_id,
							'play_id':cur_play_id,
							'play_type':cur_play_type,
							'ball':cur_ball,
							'bet_style':me.container.find('.li-style').eq(index).find('.bet-name-label-normal').html() ,
							'odds':me.container.find('.li-style').eq(index).find('.odds').html(),
							'index':index,
							'extra':me.parentPrize.parentGame.bet_max_amount
						}
					}
				}else{
					me.squareData[i] = -1;
				}
			}

			me.fireEvent('after_setSelect', me.squareData);
		},
		//更新选中项
		updateSelect:function(data){
			var me = this;
			me.container.find('.li-style').removeClass('li-style-select');
			for(var i=0;i<data.length;i++){
				if(data[i]==0){
					me.container.find('.li-style').eq(i).removeClass('li-style-select-bet').addClass('li-style-select');
				}

				if(data[i]==-1 && me.betAmountData[i]!=0){
					me.container.find('.li-style').eq(i).addClass('li-style-select-bet');
				}
			}
		},
		//修改下注金额数组
		updateBetButtonArray:function(index , value){
			var me = this;
			me.betAmountData[index] = Number(me.betAmountData[index])+Number(value);

			me.updateRealyBetButtonDate();
		},
		//更新已下注金额数组
		updateRealyBetButtonDate:function(){
			var me = this;

			for(var i=0;i<me.betAmountData.length;i++){
				if(me.betAmountData[i] > 0){
					me.container.find('.li-style').eq(i).addClass('li-style-select-bet');

					if(i<2){
						me.container.find('.li-style').eq(i).children().eq(0).addClass('bet-name-labe-inbet');
						me.container.find('.li-style').eq(i).children().eq(1).addClass('bet-odds-labe-inbet');
					}else{
						me.container.find('.li-style').eq(i).children().eq(0).addClass('bet-name-label-normal-inbet');
						me.container.find('.li-style').eq(i).children().eq(1).addClass('bet-odds-label-normal-inbet');
					}

					me.container.find('.li-style').eq(i).children().eq(2).children().eq(1).html(me.betAmountData[i]);
				}else{
					me.container.find('.li-style').eq(i).removeClass('li-style-select-bet');

					if(i<2){
						me.container.find('.li-style').eq(i).children().eq(0).removeClass('bet-name-labe-inbet');
						me.container.find('.li-style').eq(i).children().eq(1).removeClass('bet-odds-labe-inbet');
					}else{
						me.container.find('.li-style').eq(i).children().eq(0).removeClass('bet-name-label-normal-inbet');
						me.container.find('.li-style').eq(i).children().eq(1).removeClass('bet-odds-label-normal-inbet');
					}
				}
			}
		},
		//开奖时 更新组合下注区域背景
		updateZuheBetArea:function(curTotalNum){
			var me = this;

			me.container.find('.li-style').removeClass('zuhe-active-num');
			me.container.find('.li-style').removeClass('locked-button').addClass('locked-button');
				
			if(curTotalNum>13 && curTotalNum%2==1){
				me.container.find('.li-style').eq(0).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(2).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(6).removeClass('locked-button').addClass('zuhe-active-num');
			};

			if(curTotalNum>13 && curTotalNum%2==0){
				me.container.find('.li-style').eq(0).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(3).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(7).removeClass('locked-button').addClass('zuhe-active-num');
			};

			if(curTotalNum<14 && curTotalNum%2==1){
				me.container.find('.li-style').eq(1).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(2).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(8).removeClass('locked-button').addClass('zuhe-active-num');
			};

			if(curTotalNum<14 && curTotalNum%2==0){
				me.container.find('.li-style').eq(1).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(3).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(9).removeClass('locked-button').addClass('zuhe-active-num');
			};

			if(curTotalNum>=22 && curTotalNum<=27){
				me.container.find('.li-style').eq(4).removeClass('locked-button').addClass('zuhe-active-num');
			};

			if(curTotalNum>=0 && curTotalNum<=5){
				me.container.find('.li-style').eq(5).removeClass('locked-button').addClass('zuhe-active-num');
			};
		}
	};

	//html模板
	var html_all = [];
	html_all.push('<ul>');
		html_all.push('<li class="li-style li-style-1" param="0">');
			html_all.push('<span class="bet-name-label-normal bet-name-label">大</span>');
			html_all.push('<span class="bet-odds-label">x');
				html_all.push('<span class="odds daxiaodans-bsde-daxiao"></span>');
			html_all.push('</span>');
                                	html_all.push('<span class="bet-value-label-normal bet-value-label">');
                                		html_all.push('<span class="bet-chip-img"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
                                	html_all.push('</span>');
                       	html_all.push('</li>');
                       	html_all.push('<li class="li-style li-style-2" param="1">');
			html_all.push('<span class="bet-name-label-normal bet-name-label">小</span>');
			html_all.push('<span class="bet-odds-label">x');
				html_all.push('<span class="odds daxiaodans-bsde-daxiao"></span>');
			html_all.push('</span>');
                    		html_all.push('<span class="bet-value-label-normal bet-value-label">');
                                		html_all.push('<span class="bet-chip-img"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
                                	html_all.push('</span>');
                       	html_all.push('</li>');
		html_all.push('<li class="li-style" param="2">');
			html_all.push('<span class="bet-name-label-normal">单</span>');
			html_all.push('<span class="bet-odds-label-normal">x');
				html_all.push('<span class="odds daxiaodans-bsde-danshuang"></span>');
			html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style" param="3">');
			html_all.push('<span class="bet-name-label-normal">双</span>');
			html_all.push('<span class="bet-odds-label-normal">x');
				html_all.push('<span class="odds daxiaodans-bsde-danshuang"></span>');
			html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style" param="4">');
			html_all.push('<span class="bet-name-label-normal">极大</span>');
			html_all.push('<span class="bet-odds-label-normal">x');
				html_all.push('<span class="odds daxiaodans-bsde-liangji"></span>');
			html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style li-style-3" param="5">');
			html_all.push('<span class="bet-name-label-normal">极小</span>');
			html_all.push('<span class="bet-odds-label-normal">x');
				html_all.push('<span class="odds daxiaodans-bsde-liangji"></span>');
			html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style" param="6">');
			html_all.push('<span class="bet-name-label-normal">大单</span>');
                            	html_all.push('<span class="bet-odds-label-normal">x');
                            		html_all.push('<span class="odds daxiaodans-bsde-chuanguan-11"></span>');
                            	html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style" param="7">');
			html_all.push('<span class="bet-name-label-normal">大双</span>');
                            	html_all.push('<span class="bet-odds-label-normal">x');
                            		html_all.push('<span class="odds daxiaodans-bsde-chuanguan-10"></span>');
                            	html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style" param="8">');
			html_all.push('<span class="bet-name-label-normal">小单</span>');
                            	html_all.push('<span class="bet-odds-label-normal">x');
                            		html_all.push('<span class="odds daxiaodans-bsde-chuanguan-01"></span>');
                            	html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style li-style-3" param="9">');
			html_all.push('<span class="bet-name-label-normal">小双</span>');
                                	html_all.push('<span class="bet-odds-label-normal">x');
                                		html_all.push('<span class="odds daxiaodans-bsde-chuanguan-00"></span>');
                                	html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
	html_all.push('</ul>');
	
	
	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list.playlist[defConfig.name] = Main;
})(bomao, bomao.Event);
(function(host, Event, undefined){
	var defConfig = {
		name:'hezhi',
		parentPrize:null,
		container:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.parentPrize = cfg.parentPrize;
			me.squareData = [-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1];
			//已下注金额数组
			me.betAmountData = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
			me.current_cell_data = null;
			me.last_random = -1;

			me.addEvent('after_setSelect', function(e, data){
				me.updateSelect(data);
			});

			me.addEvent('afterReSet', function(){
				me.updateSelect(me.squareData);
			});
		},
		//复位数据
		rebuildData:function(){
			var me = this;
			//0-27
			me.squareData = [-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1];
		},
		//建立UI模型
		buildUI:function(){
			var me = this;
			me.container.html(html_all.join(''));

			me.updateRealyBetButtonDate();
		},
		//恢复初始样式
		reSet:function(){
			var me = this;
			me.last_random = -1;

			for(var i in me.squareData){
				me.squareData[i] = -1;
			}

			me.fireEvent('afterReSet');
		},
		//判断是否处于活跃状态
		isActivity:function(){
			var me = this;

			for(var i in me.squareData){
				if(me.squareData[i] == 0){
					return true;
					break;
				}
			}

			return false;
		},
		//设置选择项
		completeSelect:function(index){
			var me = this;
			i=0,
			len = me.squareData.length;

			var cur_play_id = me.parentPrize.pladIdArray[4];
			var cur_play_type = 'hezhi.hezhi.hezhi';

			for(;i<len;i++){
				if(i==index){
					me.squareData[index] = (me.squareData[index] == 0?-1:0);

					if(me.squareData[index] == 0){
						me.current_cell_data = {
							'city':me.parentPrize.parentGame.name,
							'prize_id':me.parentPrize.prize_id,
							'play_id':cur_play_id,
							'play_type':cur_play_type,
							'bet_style': '和',
							'ball':me.container.find('li').eq(index).find('.bet-num').html(),
							'odds':me.container.find('li').eq(index).find('.hezhi-odds-tip').children().eq(1).html(),
							'index':index,
							'extra':(me.parentPrize.limite_extra[index]>=me.parentPrize.parentGame.bet_max_amount?me.parentPrize.parentGame.bet_max_amount:me.parentPrize.limite_extra[index])
						}
					}
				}else{
					me.squareData[i] = -1;
				}
			}
			
			me.fireEvent('after_setSelect', me.squareData);
		},
		//更新选中项
		updateSelect:function(data){
			var me = this;
			me.container.find('.bet-num').removeClass('hezhi-li-style-select');
			for(var i=0;i<data.length;i++){
				if(data[i]==0){
					me.container.find('.bet-num').eq(i).removeClass('hezhi-li-style-select-bet').addClass('hezhi-li-style-select');
				}

				if(data[i]==-1 && me.betAmountData[i]!=0){
					me.container.find('.bet-num').eq(i).addClass('hezhi-li-style-select-bet');
				}
			}
		},
		//修改下注金额数组
		updateBetButtonArray:function(index , value){
			var me = this;
			me.betAmountData[index] = Number(me.betAmountData[index])+Number(value);

			me.updateRealyBetButtonDate();
		},
		//更新已下注金额数组
		updateRealyBetButtonDate:function(){
			var me = this;
			for(var i=0;i<me.betAmountData.length;i++){
				if(me.betAmountData[i] > 0){
					me.container.find('.bet-num').eq(i).addClass('hezhi-li-style-select-bet');

					me.container.find('.hezhi-bet-money').eq(i).html("￥"+me.betAmountData[i]+"&nbsp;x&nbsp;");
				}else{
					me.container.find('.bet-num').eq(i).removeClass('hezhi-li-style-select-bet');

					me.container.find('.hezhi-bet-money').eq(i).html("&nbsp;x&nbsp;");
				}
			}
		},
		//开奖时 更新和值下注区域背景
		updateHezhiBetArea:function(curTotalNum){
			var me = this;
			var clsStr = '.bet-num-'+curTotalNum;

			me.container.find('.bet-num').removeClass('hezhi-active-num');
			me.container.find('.bet-num').removeClass('locked-button').addClass('locked-button');

			me.container.find(clsStr).removeClass('locked-button').addClass('hezhi-active-num');
		}

		
	};

	//html模板
	var html_all = [];
	html_all.push('<ul>');
		$.each([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27],function(){
			html_all.push('<li param='+this+'>');
				html_all.push('<a class="bet-num bet-num-'+this+'">'+this+'</a>');
				html_all.push('<span class="hezhi-odds-tip">');
					html_all.push('<span class="hezhi-bet-money"></span>');
					html_all.push('<span class="tip-'+this+'"></span>');
				html_all.push('</span>');
			html_all.push('</li>');
		});
	html_all.push('</ul>');

	html_all.push('<ul class="hezhi-random-box">');
		html_all.push('<li><span class="odds-explain"><span class="odds-help-logo">?</span>&nbsp;赔率说明</span></li>');
		html_all.push('<li><span class="random-box random-submit">机选</span></li>');
		// html_all.push('<li><span class="random-box random-cancel">取消</span></li>');
	html_all.push('</ul>');
	
	
	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list.playlist[defConfig.name] = Main;
})(bomao, bomao.Event);
(function(host, Event, undefined){
	var defConfig = {
		name:'informationtimer',
		container:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.leftTime = '';
			me.parentPrize = null;
		},
		//复位数据
		rebuildData:function(){
			var me = this;
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));
			me.container.find('.prize_number').html("No."+me.getParentPrize().prize_id);
			//游戏名称
			switch(me.parentPrize.parentGame.name){
				case "重庆":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" CQ");break;
				case "黑龙江":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" HLJ");break;
				case "新疆":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" XJ");break;
				case "天津":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" TJ");break;
				case "北京":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" BJ");break;
				case "上海":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" SH");break;
				case "博猫":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Bomao");break;
				case "韩国":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Korea");break;
				case "斯洛伐克":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+"S lovakia");break;
				case "土耳其":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Turkey");break;
				case "加拿大":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Canada");break;
				default:me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name);break;
			};
		},
		getParentPrize:function(){
			var me = this;
			return me.parentPrize;
		},
		setParentPrzie:function(prize){
			var me = this;
			me.parentPrize = prize;
		},
		setLeftTime:function(leftTime){
			var me = this;
			me.leftTime = leftTime;

			me.old_time = new Date();
			
			if(leftTime<=me.parentPrize.cycleTime){
				me.timer = setInterval(function(){
					me.updateTimer();
				} , 500);

				me.updateTimer();
			}else{
				me.parentPrize.parentGame.showDeadLine('---');
			}
		},
		//更新倒计时
		updateTimer:function(){
			var me = this;
			
			// var new_time = new Date();
			
			var dis_time = ($('#new-service-time').val()-me.old_time.getTime())>0?$('#new-service-time').val()-me.old_time.getTime():0;
			//计算时间差 
			var time_distance = parseInt((me.leftTime*1000-dis_time)/1000);

			if(me.leftTime > 0){
				//计算、显示秒数
				var end_time = '';

				if(time_distance <= 0){
					end_time = "000";
				}else{
					switch(String(time_distance).length){
						case 2 : end_time = "0"+time_distance;break;
						case 1 : end_time = "00"+time_distance;break;
						default : end_time = time_distance;break;
					}
				}

				me.parentPrize.parentGame.showDeadLine(end_time);
			}
			
			
			me.container.find(".deadseconds").html(me.parentPrize.entertainedTime);
			if(parseInt(time_distance)>=(Number(me.parentPrize.entertainedTime)+31)){
				me.container.find(".prize_tips").removeClass().addClass('prize_tips').addClass('prize_tips_hide');
			}else{
				me.container.find(".prize_tips").removeClass().addClass('prize_tips').addClass('prize_tips_show');
				//开奖时间少于40s，进行封盘动作
				if(parseInt(time_distance)<=Number(me.parentPrize.entertainedTime)){
					//状态0 -> 状态1
					if(me.getParentPrize().status == 0){
						me.getParentPrize().status = 1;
						me.getParentPrize().fireEvent("change_prize_status" , me.getParentPrize());
						me.getParentPrize().container.find('bet-panel-mask').show();
					}
					//状态1 -> 状态2
					if(me.getParentPrize().status == 1 && parseInt(time_distance)<=0){
						me.getParentPrize().status = 2;
						me.getParentPrize().fireEvent("change_prize_status" , me.getParentPrize());
					}
					
				}
			}

			if(time_distance>=0){
				// 时
				var int_hour = Math.floor(time_distance/3600) 
				time_distance -= int_hour * 3600; 
				// 分
				var int_minute = Math.floor(time_distance/60) 
				time_distance -= int_minute * 60; 
				// 秒 
				var int_second = Math.floor(time_distance) 
				// 时分秒为单数时、前面加零 
				/*
				if(int_hour < 10){ 
					int_hour = "0" + int_hour; 
				} 
				*/
				if(int_minute < 10){ 
					int_minute = "0" + int_minute; 
				} 
				if(int_second < 10){
					int_second = "0" + int_second; 
				} 
				
				// 显示时间
				me.container.find(".time_h").html(int_hour);
				me.container.find(".time_m").html(int_minute);
				me.container.find(".time_s").html(int_second);

			}else{
				clearInterval(me.timer);

				// 显示时间 
				me.container.find(".time_h").html("00");
				me.container.find(".time_m").html("00");
				me.container.find(".time_s").html("00");
			}
		},
	};

	var html_all = [];
	html_all.push('<ul class="timer_ul">');
		html_all.push('<li><span class="prize_label">即将开奖</span></li>');
		html_all.push('<li><span class="prize_number">No.</span></li>');
		html_all.push('<li>');
			html_all.push('<div class="prize_timer">');
				html_all.push('<span class="time_txt time_h" style="display: none"></span>');
		               	html_all.push(' <span class="time_txt" style="display: none">:</span>');
		                	html_all.push('<span class="time_txt time_m"></span>');
		                	html_all.push('<span class="time_txt">:</span>');
		                	html_all.push('<span class="time_txt time_s"></span>');
			html_all.push('</div>');
		html_all.push('</li>');
		html_all.push('<li><span class="prize_tips">最后<span class="deadseconds"></span>秒截止受注</span></li>');
	html_all.push('</ul>');
	html_all.push('<div class="prize_city"><span class="prize_city_lab"></span></div>');

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list.informationlist[defConfig.name] = Main;
})(bomao, bomao.Event);
(function(host, Event, undefined){
	var defConfig = {
		name:'informationwait',
		container:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.parentPrize = null;
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));
			//游戏名称
			switch(me.parentPrize.parentGame.name){
				case "重庆":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" CQ");break;
				case "黑龙江":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" HLJ");break;
				case "新疆":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" XJ");break;
				case "天津":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" TJ");break;
				case "北京":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" BJ");break;
				case "上海":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" SH");break;
				case "博猫":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Bomao");break;
				case "韩国":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Korea");break;
				case "斯洛伐克":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Slovakia");break;
				case "土耳其":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Turkey");break;
				case "加拿大":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Canada");break;
				default:me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name);break;
			};
		},
	};

	var html_all = [];
	html_all.push('<div class="waiting_box">');
		html_all.push('<span class="wait_icon"></span>');
		html_all.push('<span class="wait_label">等待开奖中...</span>');
	html_all.push('</div>');
	html_all.push('<div class="prize_city"><span class="prize_city_lab"></span></div>');

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list.informationlist[defConfig.name] = Main;
})(bomao, bomao.Event);
(function(host, Event, undefined){
	var defConfig = {
		name:'informationresult',
		container:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.parentPrize = null;
			
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));
			//游戏名称
			switch(me.parentPrize.parentGame.name){
				case "重庆":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" CQ");break;
				case "黑龙江":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" HLJ");break;
				case "新疆":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" XJ");break;
				case "天津":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" TJ");break;
				case "北京":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" BJ");break;
				case "上海":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" SH");break;
				case "博猫":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Bomao");break;
				case "韩国":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Korea");break;
				case "斯洛伐克":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Slovakia");break;
				case "土耳其":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Turkey");break;
				case "加拿大":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Canada");break;
				default:me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name);break;
			};

			me.container.find('.current-ball').get(0).addEventListener("animationend",function(){
				me.container.find('.current-ball').removeClass('bounceInLow').removeClass('bounceInLowTwo').removeClass('bounceInLowThree');
				me.container.find('.current-ball').removeClass('show-model').addClass('hide-model');
				switch(me.animate_time){
					case 4: 
						me.container.find('.num-1').removeClass('hide-model').addClass('show-model');
						break;
					case 3: 
						me.container.find('.num-1').removeClass('hide-model').addClass('show-model');
						me.container.find('.num-2').removeClass('hide-model').addClass('show-model');
						break;
					case 2: 
						me.container.find('.num-1').removeClass('hide-model').addClass('show-model');
						me.container.find('.num-2').removeClass('hide-model').addClass('show-model');
						me.container.find('.num-3').removeClass('hide-model').addClass('show-model');
						break;
					default:break;
				}
			});
			me.container.find('.current-ball').get(0).addEventListener("webkitAnimationEnd",function(){
				me.container.find('.current-ball').removeClass('bounceInLow').removeClass('bounceInLowTwo').removeClass('bounceInLowThree');
				me.container.find('.current-ball').removeClass('show-model').addClass('hide-model');
				switch(me.animate_time){
					case 4: 
						me.container.find('.num-1').removeClass('hide-model').addClass('show-model');
						break;
					case 3: 
						me.container.find('.num-1').removeClass('hide-model').addClass('show-model');
						me.container.find('.num-2').removeClass('hide-model').addClass('show-model');
						break;
					case 2: 
						me.container.find('.num-1').removeClass('hide-model').addClass('show-model');
						me.container.find('.num-2').removeClass('hide-model').addClass('show-model');
						me.container.find('.num-3').removeClass('hide-model').addClass('show-model');
						break;
					default:break;
				}
			});
		},
		//开奖动画
		playAnimation:function(data){
			var me = this;
			me.animate_time = 5;

			me.container.find('.result_box').children().removeClass('show-model').addClass('hide-model');
			me.container.find('.result_lab').children().removeClass('show-model').addClass('hide-model');
			me.container.find('.result_money').children().removeClass('show-model').addClass('hide-model');
			me.container.find('.change_tips').removeClass('show-model').addClass('hide-model');

			var timer = setInterval(function(){
				switch(me.animate_time){
					case 5:
						me.container.find('.lottering-box').removeClass('hide-model').addClass('show-model');
						me.container.find('.current-ball').html(data.num_1);
						me.container.find('.current-total').html(data.num_1);

						me.container.find('.current-ball').addClass('bounceInLow');

						me.updateBetArea(Number(data.num_1));
						break;
					case 4:
						me.container.find('.current-ball').removeClass('hide-model').addClass('show-model');

						me.container.find('.current-ball').html(data.num_2);
						me.container.find('.current-total').html((Number(data.num_1)+Number(data.num_2)));

						me.container.find('.current-ball').addClass('bounceInLowTwo');

						me.updateBetArea((Number(data.num_1)+Number(data.num_2)));
						break;
					case 3:
						me.container.find('.current-ball').removeClass('hide-model').addClass('show-model');

						me.container.find('.current-ball').html(data.num_3);
						me.container.find('.current-total').html((Number(data.num_1)+Number(data.num_2)+Number(data.num_3)));

						me.container.find('.current-ball').addClass('bounceInLowThree');

						me.updateBetArea((Number(data.num_1)+Number(data.num_2)+Number(data.num_3)));
						break;
					case 2:
						me.container.find('.result_box').children().removeClass('hide-model').addClass('show-model');
						me.container.find('.result_lab').children().removeClass('hide-model').addClass('show-model');

						me.container.find('.lottering-box').removeClass('show-model').addClass('hide-model');
						me.container.find('.current-ball').html('');
						me.container.find('.current-total').html('');
						break;
					case 1:
						me.container.find('.result_box').children().removeClass('hide-model').addClass('show-model');
						me.container.find('.result_lab').children().removeClass('hide-model').addClass('show-model');
						me.container.find('.result_money').children().removeClass('hide-model').addClass('show-model');
						me.container.find('.change_tips').removeClass('hide-model').addClass('show-model');
						me.container.find('.lottering-box').hide();
						break;
					default:break;
				}

				me.animate_time--;

				if(me.animate_time<=0){
					clearInterval(timer);

					//更新走势图数据、渲染
					me.parentPrize.parentGame.mini_history.updataSourceData(me.parentPrize.resultNumberData);

					me.parentPrize.updataStatus(4);

					setTimeout(function(){

						me.parentPrize.parentGame.autoSwitchPrize(0);
						me.container.find('.change_tips').removeClass('show-model').addClass('hide-model');
						
					} , 5000);
				}
			},1200);
		},
		//更新下注区域效果
		updateBetArea:function(currentTotalNum){
			var me = this;
			//大小、单双
			me.parentPrize.play_zuhe.updateZuheBetArea(Number(currentTotalNum));
			//和值
			me.parentPrize.play_hezhi.updateHezhiBetArea(Number(currentTotalNum));

		},
		//显示开奖结果
		updateResult:function(data){
			var me = this;

			me.container.find('.num-1').html(data.num_1);
			me.container.find('.num-2').html(data.num_2);
			me.container.find('.num-3').html(data.num_3);
			me.container.find('.num-total').html(data.num_total);

			var m_val_1 = 0;//大小结算
			var m_val_2 = 0;//单双结算
			var m_val_3 = 0;//串关结算
			var m_val_4 = 0;//两级结算
			var m_val_5 = 0;//和值

			//大小
			if(data.num_total>13){
				me.container.find('.res-lab-1').html('大');
				m_val_1 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-daxiao').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[0]) - Number(me.parentPrize.play_zuhe.betAmountData[1]);
			}else{
				me.container.find('.res-lab-1').html('小');
				m_val_1 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-daxiao').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[1]) - Number(me.parentPrize.play_zuhe.betAmountData[0]);
			}
			//单双
			if(data.num_total%2==1){
				me.container.find('.res-lab-2').html('、单');
				m_val_2 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-danshuang').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[2]) - Number(me.parentPrize.play_zuhe.betAmountData[3]);
			}else{
				me.container.find('.res-lab-2').html('、双');
				m_val_2 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-danshuang').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[3]) - Number(me.parentPrize.play_zuhe.betAmountData[2]);
			}
			//极大
			if(data.num_total>=22 && data.num_total<=27){
				me.container.find('.res-lab-3').html('、极大');
				m_val_4 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-liangji').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[4]) - Number(me.parentPrize.play_zuhe.betAmountData[5]);
			}else if(data.num_total>=0 && data.num_total<=5){
			//极小
				me.container.find('.res-lab-3').html('、极小');
				m_val_4 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-liangji').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[5]) - Number(me.parentPrize.play_zuhe.betAmountData[4]);
			}else{
			//非极值
				me.container.find('.res-lab-3').html('');
				m_val_4 = -Number(me.parentPrize.play_zuhe.betAmountData[5]) - Number(me.parentPrize.play_zuhe.betAmountData[4]);
			}
			
			//和值
			me.container.find('.lab-total').html(data.num_total);

			//大小结算
			me.container.find('.money-value-1').html((m_val_1>0?'+':'')+m_val_1.toFixed(2));
			if(m_val_1==0){
				me.container.find('.money-value-1').parent().parent().hide();
			}else{
				me.container.find('.money-value-1').parent().parent().show();
				if(m_val_1>0){
					me.container.find('.money-value-1').parent().find('.money-lab').addClass('money-lab-win');
					me.container.find('.money-value-1').parent().find('.money-value').addClass('money-value-win');
				}
			}
			//单双结算
			me.container.find('.money-value-2').html((m_val_2>0?'+':'')+m_val_2.toFixed(2));
			if(m_val_2==0){
				me.container.find('.money-value-2').parent().parent().hide();
			}else{
				me.container.find('.money-value-2').parent().parent().show();
				if(m_val_2>0){
					me.container.find('.money-value-2').parent().find('.money-lab').addClass('money-lab-win');
					me.container.find('.money-value-2').parent().find('.money-value').addClass('money-value-win');
				}
			}
			//两级结算
			me.container.find('.money-value-4').html((m_val_4>0?'+':'')+m_val_4.toFixed(2));
			if(m_val_4==0){
				me.container.find('.money-value-4').parent().parent().hide();
			}else{
				me.container.find('.money-value-4').parent().parent().show();
				if(m_val_4>0){
					me.container.find('.money-value-4').parent().find('.money-lab').addClass('money-lab-win');
					me.container.find('.money-value-4').parent().find('.money-value').addClass('money-value-win');
				}
			}
			//大单、大双、小单、小双
			if(data.num_total>13 && data.num_total%2==1){
				m_val_3 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-chuanguan-11').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[6])-
					Number(me.parentPrize.play_zuhe.betAmountData[7])-
					Number(me.parentPrize.play_zuhe.betAmountData[8])-
					Number(me.parentPrize.play_zuhe.betAmountData[9]);
			}
			if(data.num_total>13 && data.num_total%2==0){
				m_val_3 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-chuanguan-10').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[7])-
					Number(me.parentPrize.play_zuhe.betAmountData[6])-
					Number(me.parentPrize.play_zuhe.betAmountData[8])-
					Number(me.parentPrize.play_zuhe.betAmountData[9]);
			}
			if(data.num_total<14 && data.num_total%2==1){
				m_val_3 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-chuanguan-01').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[8])-
					Number(me.parentPrize.play_zuhe.betAmountData[6])-
					Number(me.parentPrize.play_zuhe.betAmountData[7])-
					Number(me.parentPrize.play_zuhe.betAmountData[9]);
			}
			if(data.num_total<14 && data.num_total%2==0){
				m_val_3 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-chuanguan-00').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[9])-
					Number(me.parentPrize.play_zuhe.betAmountData[6])-
					Number(me.parentPrize.play_zuhe.betAmountData[7])-
					Number(me.parentPrize.play_zuhe.betAmountData[8]);
			}
			//串关结算
			me.container.find('.money-value-3').html((m_val_3>0?'+':'')+m_val_3.toFixed(2));
			if(m_val_3==0){
				me.container.find('.money-value-3').parent().parent().hide();
			}else{
				me.container.find('.money-value-3').parent().parent().show();
				if(m_val_3>0){
					me.container.find('.money-value-3').parent().find('.money-lab').addClass('money-lab-win');
					me.container.find('.money-value-3').parent().find('.money-value').addClass('money-value-win');
				}
			}

			//和值结算			
			for(var i in me.parentPrize.play_hezhi.betAmountData){
				var cls = '.tip-'+i;
				if(i == Number(data.num_total)){
					m_val_5 = Number(m_val_5)+(Number(me.parentPrize.container.find(cls).html())-1)*Number(me.parentPrize.play_hezhi.betAmountData[i]);
					if(me.parentPrize.status == 4){
						me.updateBetArea(i);
					}
					
				}else{
					m_val_5 = Number(m_val_5) - Number(me.parentPrize.play_hezhi.betAmountData[i]);
				}
			}
			
			if(m_val_5==0){
				me.container.find('.money-value-5').parent().parent().hide();
			}else{
				me.container.find('.money-value-5').parent().parent().show();
				if(m_val_5>0){
					me.container.find('.money-value-5').parent().find('.money-lab').addClass('money-lab-win');
					me.container.find('.money-value-5').parent().find('.money-value').addClass('money-value-win');
				}
			}

			me.container.find('.money-value-5').html((m_val_5>0?'+':'')+m_val_5.toFixed(2));
		}
	};

	var html_all = [];
	html_all.push('<div class="result_box">');
		html_all.push('<ul class="result_num">');
			html_all.push('<li><span class="number num-1"></span></li>');
			html_all.push('<li><span class="sign">+</span></li>');
			html_all.push('<li><span class="number num-2"></span></li>');
			html_all.push('<li><span class="sign">+</span></li>');
			html_all.push('<li><span class="number num-3"></span></li>');
			html_all.push('<li><span class="sign">=</span></li>');
			html_all.push('<li><span class="num-total"></span></li>');
		html_all.push('</ul>');

		html_all.push('<ul class="result_lab">');
			html_all.push('<li><span class="lab res-lab-1"></span></li>');
			html_all.push('<li><span class="lab res-lab-2"></span></li>');
			html_all.push('<li><span class="lab res-lab-3"></span></li>');
			html_all.push('<li><span class="blank"></span></li>');
			html_all.push('<li><span class="lab">和</span></li>');
			html_all.push('<li><span>:</span></li>');
			html_all.push('<li><span class="lab lab-total"></span></li>');
		html_all.push('</ul>');
	html_all.push('</div>');
	
	html_all.push('<div class="result_money">');
		html_all.push('<ul>');
			html_all.push('<li><span class="money"><span class="money-lab">大小</span><span class="money-value money-value-1"></span></li>');
			html_all.push('<li><span class="money"><span class="money-lab">单双</span><span class="money-value money-value-2"></span></li>');
			html_all.push('<li><span class="money"><span class="money-lab">串关</span><span class="money-value money-value-3"></span></li>');
			html_all.push('<li><span class="money"><span class="money-lab">两级</span><span class="money-value money-value-4"></span></li>');
			html_all.push('<li><span class="money"><span class="money-lab">和</span><span class="money-value money-value-5"></span></li>');
		html_all.push('</ul>');
	html_all.push('</div>');

	html_all.push('<div class="lottering-box hide-model">');
		html_all.push('<span class="current-ball"></span>');
		html_all.push('<span class="current-total"></span>');
	html_all.push('</div>');
	
	
	html_all.push('<span class="change_tips hide-model">5秒后自动切换</span>');
	html_all.push('<div class="prize_city"><span class="prize_city_lab"></span></div>');

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list.informationlist[defConfig.name] = Main;

})(bomao, bomao.Event);
(function(host, Event, undefined){
	var defConfig = {
		name:'informationSuspension',
		container:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.parentPrize = null;
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));
			//游戏名称
			switch(me.parentPrize.parentGame.name){
				case "重庆":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" CQ");break;
				case "黑龙江":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" HLJ");break;
				case "新疆":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" XJ");break;
				case "天津":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" TJ");break;
				case "北京":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" BJ");break;
				case "上海":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" SH");break;
				case "博猫":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Bomao");break;
				case "韩国":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Korea");break;
				case "斯洛伐克":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Slovakia");break;
				case "土耳其":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Turkey");break;
				case "加拿大":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Canada");break;
				default:me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name);break;
			};
		},
		//更新开盘时间
		updateOpenTime:function(month,day,hour,minute,second){
			var me = this;
			var date = month+'月'+day+'日 '+(hour>=10?hour:'0'+hour)+':'+(minute>=10?minute:'0'+minute)+':'+(second>=10?second:'0'+second);
			me.container.find('.suspension-lab-2').html(date);
		}
	};

	var html_all = [];
	html_all.push('<span class="suspension-lab suspension-lab-1">该彩种官网目前已停盘，预计开奖时间:</span>');
	html_all.push('<span class="suspension-lab suspension-lab-2"></span>');
	html_all.push('<span class="suspension-lab suspension-lab-3">给您造成的不便敬请见谅!</span>');
	html_all.push('<div class="prize_city"><span class="prize_city_lab"></span></div>');

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list.informationlist[defConfig.name] = Main;
})(bomao, bomao.Event);
(function(host, Event, undefined){
	var defConfig = {
		name:'orderWindow',
		//父类容器
		UIContainer:'#orderWindow',
		//自身游戏容器
		container : '',
		service : null
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.service=cfg.service;
			me.current_menu = 0;
			me.cell_data = null;
			me.UIContainer = $(cfg.UIContainer);
			me.game = null;
			me.lastgame = null;
			me.current_order_data = null;
			me.container = $('<div class="order-panel order-panel-hide"></div>').appendTo($(me.UIContainer));
			me.userAccount = 0;
			me.bet_value_total = 0;
			me.win_value_total = 0;
			//当前订单数组
			me.cur_orders_arr = [];
			//当前撤单 订单id数组
			me.cur_cancel_orders_arr=[];
			//是否处于拖动
			me.drag=false;
			////记录鼠标点击的点 与 订单模块左上角的距离
			me._x = 0;
			me._y = 0;

			me.buildUI();

			me.addEvent('afert_select_order_menu' ,function(e ,data){
				me.showContent(data);
			});
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			//设置窗口位置
			me.container.css({
				left:(document.body.scrollWidth-$('.lucky-main-panel').outerWidth())/2 + $('.game').width() + 10
			});
			me.container.html(html_all.join(''));
		},
		//更新菜单信息
		updateContent:function(){
			var me = this;
			
			me.container.find('.order-city-name').html(me.game.name);
			me.container.find('.prize-id').html('NO.'+me.cell_data.prize_id);
			me.container.find('.bet-style').html(me.cell_data.bet_style);

			if(me.game.getCurrentPrize().currentPlayIndex == 1){
				if(me.container.find('.total-value').hasClass('total-value-hide')){
					me.container.find('.total-value').removeClass('total-value-hide').addClass('total-value-show');
				}
				me.container.find('.total-value').html(me.cell_data.ball);
			}else{
				if(me.container.find('.total-value').hasClass('total-value-show')){
					me.container.find('.total-value').removeClass('total-value-show').addClass('total-value-hide');
				}
				me.container.find('.total-value').addClass('total-value-hide');
			};
			me.container.find('.odds').html(me.cell_data.odds);

			me.container.find('.limit-value-lab').html("1.00-"+me.cell_data.extra+".00");

			me.getGameOrders(me.game.id);
		},
		//显示菜单
		showOrderWindow:function(game , cell_data){
			var me = this;

			me.game = game;
			//不同游戏之间 玩法切换时,还原前一次玩法面板
			me.container.find('.money-box').val('请输入下注金额');
			if(me.lastgame != me.game){
				if(me.lastgame){
					me.lastgame.getCurrentPrize().play_hezhi.reSet();
					me.lastgame.getCurrentPrize().play_zuhe.reSet();
				}
				me.lastgame = me.game;
			}

			me.cell_data = cell_data;

			me.current_order_data  = {
				"gameId":game.id,
				"isTrace":"0",
				"traceWinStop":"1",
				"traceStopValue":"1",
				"balls":[
					{
						"jsId":"1",
						"wayId":me.cell_data.play_id,
						"ball":me.cell_data.ball,
						"viewBalls":"",
						"num": "1",
						"type":cell_data.play_type,
						"onePrice":"1",
						"moneyunit":"1",
						"multiple":"1",
						"prize_group":me.game.prize_group
					}

				],
				"orders":{},
				"amount":'1',
				"_token":game._token
			};

			me.current_order_data["orders"][me.cell_data.prize_id] = 1;

			if(me.container.hasClass('order-panel-hide')){
				me.container.removeClass('order-panel-hide').addClass('order-panel-show');
				me.getUserAccount();
			}

			me.updateContent();
			// me.container.find('.money-box').focus();

			//默认切换成下注界面
			me.switchMenu(0);
		},
		//获取账号余额
		getUserAccount:function(){
			var me = this;
			me.service.getUserAccount(function(data){
				me.userAccount = parseFloat(data.data[0].data[0].data);

				me.userAccount = Math.round(me.userAccount*100)/100;
				var money = me.formatMoney(me.userAccount,2);

				me.container.find('.user-account').html(money);
				//强制刷新页面上方的余额
				$('#J-top-user-balance').html(money);
			});
		},
		//金额格式化
		formatMoney:function(money, digit){
			var tpMoney = '0.00';

			if(undefined != money){  
				tpMoney = money;  
			}

			tpMoney = new Number(tpMoney);  
			if(isNaN(tpMoney)){  
				return '0.00';  
			}

			tpMoney = tpMoney.toFixed(digit) + '';
			var re = /^(-?\d+)(\d{3})(\.?\d*)/;

			while(re.test(tpMoney)){  
				tpMoney = tpMoney.replace(re, "$1,$2$3")  
			}  

			return tpMoney;  
		},
		//关闭菜单
		closeOrder:function(){
			var me = this;
			//回复默认
			me.container.find('.money-box').val('请输入下注金额');
			if(me.container.hasClass('order-panel-show')){
				me.container.removeClass('order-panel-show').addClass('order-panel-hide');
			}
			me.switchMenu(0);
			me.container.find("input[name='all_select_box']").attr("checked",false);
			/*玩法样式要复原*/
			me.game.getCurrentPrize().play_hezhi.reSet();
			me.game.getCurrentPrize().play_zuhe.reSet();
		},
		//切换菜单
		switchMenu:function(index){
			var me = this;
			me.current_menu = index;
			if(index==1){
				me.container.find('.order-submit').html('确认撤单');
			}else{
				me.container.find('.order-submit').html('确认下单');
			}

			me.fireEvent('afert_select_order_menu' ,me.current_menu);
		},
		//切换标签，显示对应的标签内容
		showContent:function(data){
			var me = this;
			me.container.find('.tag-menu').removeClass('menu-active');
			me.container.find('.tag-menu-sign').removeClass('tag-menu-sign-active');
			me.container.find('.tag-menu').eq(data).addClass('menu-active');
			me.container.find('.tag-menu-sign').eq(data).addClass('tag-menu-sign-active');

			me.container.find('.details-panel').children().removeClass('content-show');
			me.container.find('.details-panel').children().eq(data).addClass('content-show');
			
		},
		//更新下注金额
		updateBetAmount:function(num){
			var me = this;
			me.current_order_data.balls[0].multiple = num;
		},
		//提交订单
		submitOrder:function(){
			var me = this;
			//更新下注金额
			if(me.container.find('.money-box').val() == "请输入下注金额"){
				me.updateBetAmount(1);
			}else{
				me.updateBetAmount(me.container.find('.money-box').val());
			}
			
			if((Number(me.current_order_data.balls[0].multiple)+Number(me.bet_value_total))>(me.cell_data.extra)){
				//提醒
				var message = new bomao.GameMessage();
				message.showTip('抱歉，下注金额已经到达最大额限！');
				var sNum = 1;
				var timer = setInterval(function(){
					sNum -= 1;
					if(sNum < 0){
						clearInterval(timer);
						message.hideTip();
					}
				}, 1 * 500);
			}else{
				me.service.sumbitOrder(me.game.id,me.current_order_data,function(data){
					//回复默认1元
					me.container.find('.money-box').val('1');
					//更新余额信息
					me.getUserAccount();
					//更新订单信息
					me.getGameOrders(me.game.id);

					if(data.isSuccess == 1){
						if(me.game.getCurrentPrize().currentPlayIndex == 0){
							me.game.getCurrentPrize().play_zuhe.updateBetButtonArray(me.cell_data.index , me.current_order_data.balls[0].multiple);
						}else{
							me.game.getCurrentPrize().play_hezhi.updateBetButtonArray(me.cell_data.index , me.current_order_data.balls[0].multiple);
						}
					}
				});
				me.closeOrder();
			}
		},
		//提交取消订单(撤单)
		submitCancelOrder:function(){
			var me = this;
			if(me.cur_cancel_orders_arr.length != 0){
				me.service.cancelOrder(me.cur_cancel_orders_arr , me.cell_data.prize_id , me.game.id , me.game._token , function(data){
					//订单数组
					var arr = [];
					for(var i in me.cur_orders_arr){
						arr.push(me.cur_orders_arr[i]);
					}
					
					//回复默认1元
					me.container.find('.money-box').val('1');
					//更新余额信息
					me.getUserAccount();
					//更新订单信息
					me.getGameOrders(me.game.id);

					if(data.isSuccess == 1){
						if(me.game.getCurrentPrize().currentPlayIndex == 0){
							for(var i in arr){
								for(var j in me.cur_cancel_orders_arr){
									if(arr[i].id == me.cur_cancel_orders_arr[j]){
										me.game.getCurrentPrize().play_zuhe.updateBetButtonArray(me.cell_data.index , -parseInt(arr[i].bet_value));
									}
								}
							}
			
						}else{
							for(var i in arr){
								for(var j in me.cur_cancel_orders_arr){
									if(arr[i].id == me.cur_cancel_orders_arr[j]){
										me.game.getCurrentPrize().play_hezhi.updateBetButtonArray(me.cell_data.index , -parseInt(arr[i].bet_value));
									}
								}
							}
						}
					}

					me.container.find("input[name='all_select_box']").attr("checked",false);

					me.cur_cancel_orders_arr = [];

					me.closeOrder();
				});
			}else{
				//提醒
				var message = new bomao.GameMessage();
				message.showTip('请选择至少一条订单');
				var sNum = 1;
				var timer = setInterval(function(){
					sNum -= 1;
					if(sNum < 0){
						clearInterval(timer);
						message.hideTip();
					}
				}, 1 * 500);
			}
		},
		//取消下注
		cancelOrder:function(){
			var me = this;
			me.container.find('.money-box').val('1');
			//更新下注金额
			me.updateBetAmount(1);
			//取消-强制关闭
			me.closeOrder();
		},
		//取消要撤单的订单
		cancelSelectOrder:function(){
			var me = this;
			me.cur_cancel_orders_arr = [];
			me.container.find("input[name='all_select_box']").prop("checked", false);
			me.container.find("input[name='cancel_order']").prop("checked", false);
			//取消-强制关闭
			me.closeOrder();
		},
		//获取某一游戏的订单信息
		getGameOrders:function(gameId){
			var me = this;
			me.container.find('.order-list-content-box').html('');
			me.bet_value_total = 0;
			me.win_value_total = 0;
			me.cur_orders_arr = [];
			me.service.getOrders(gameId , function(data){
				me.container.find('.order-list-content-box').html('');
				var ordersArray = data.data[0].data;
				me.updateOrderInformation(ordersArray);
			});
		},
		//显示当前奖期,所选号码的订单信息
		updateOrderInformation:function(orderData){
			var me = this;
			for(var i in orderData){
				if(me.cell_data.prize_id == orderData[i].number && orderData[i].status != "已撤销" && (me.cell_data.bet_style == orderData[i].balls || (me.cell_data.bet_style == '和'&&me.cell_data.ball == orderData[i].balls))){
					var win_value_num = (parseFloat(orderData[i].money)*parseFloat(me.cell_data.odds)).toFixed(2);
					var order = new bomao.Lucky28.order({id:orderData[i].id , bet_value:orderData[i].money , win_value:win_value_num});
					order.buildUI();
					me.cur_orders_arr.push(order);

					me.bet_value_total = parseFloat(me.bet_value_total) + parseFloat(orderData[i].money);
					me.win_value_total = parseFloat(me.win_value_total) + parseFloat(win_value_num);
				}
			}

			me.container.find('.win-value-lab').html(parseFloat(me.win_value_total).toFixed(2));
			me.container.find('.realy-bet-value-lab').html(parseFloat(me.bet_value_total).toFixed(2));

			if(me.cell_data.extra-me.bet_value_total <= 0){
				me.container.find('.limit-value-lab').html("0.00");
			}else{
				me.container.find('.limit-value-lab').html("1.00-"+(me.cell_data.extra-me.bet_value_total)+".00");
			}
		}
	};


	var html_all = [];
	html_all.push('<div class="order-menu-head">');
		html_all.push('<div class="close-order"></div>');
		html_all.push('<ul class="order-tag">');
			html_all.push('<li><span class="tag-menu menu-active" param="0">下单详情</span><span class="tag-menu-sign tag-menu-sign-active"></span></li>');
			html_all.push('<li><span class="tag-menu" param="1">快速撤单</span><span class="tag-menu-sign"></span></li>');
		html_all.push('</ul>');
	html_all.push('</div>');
	html_all.push('<div class="order-content">');
		html_all.push('<ul>');
			html_all.push('<li class="money-information"><span>钱包：</span><span class="user-account"></span></li>');
			html_all.push('<li class="order-city"><span class="order-city-name"></span>&nbsp;&nbsp;<span class="prize-id"></span></li>');
			
			html_all.push('<li class="order-bet-information">');
				html_all.push('<ul class="bet-details">');
					html_all.push('<li><span class="bet-style">和</span></li>');
					html_all.push('<li><span class="total-value total-value-hide"></span></li>');
					html_all.push('<li><span>x&nbsp;</span><span class="odds"></span></li>');
				html_all.push('</ul>');
			html_all.push('</li>');
		html_all.push('</ul>');

		html_all.push('<span class="order-city-name order-city-name-lab"></span>');
		
		html_all.push('<div class="details-panel">');
			html_all.push('<div class="order-details content-show">');
				html_all.push('<ul >');
					html_all.push('<li>');

						html_all.push('<input class="money-box" type="text" value="请输入下注金额"></input>');
						html_all.push('<ul class="money-list">');
							html_all.push('<li param="all"><span></span></li>');
							html_all.push('<li class="normal-money-box" param="1"><span>1</span></li>');
							html_all.push('<li class="normal-money-box" param="2"><span>2</span></li>');
							html_all.push('<li class="normal-money-box" param="5"><span>5</span></li>');
							html_all.push('<li class="normal-money-box" param="10"><span>10</span></li>');
							html_all.push('<li class="normal-money-box" param="50"><span>50</span></li>');
							html_all.push('<li class="normal-money-box" param="100"><span>100</span></li>');
							html_all.push('<li class="normal-money-box" param="500"><span>500</span></li>');
						html_all.push('</ul>');

					html_all.push('</li>');
					html_all.push('<li class="bet-list-lab"><span>可赢金额: </span><span class="win-value-lab">0.00</span></li>');
					html_all.push('<li class="bet-list-lab"><span>已投金额: </span><span class="realy-bet-value-lab">0.00</span></li>');
					html_all.push('<li class="bet-list-lab"><span>下注限额: </span><span class="limit-value-lab">1.00-1000.00</span></li>');
				html_all.push('</ul>');
			html_all.push('</div>');
			//撤单信息面板
			html_all.push('<div class="revocation-list">');
				html_all.push('<ul class="order-list-head">');
					html_all.push('<li class="order-list-head-content">');
						html_all.push('<span>全选&nbsp;&nbsp;</span>');
						html_all.push('<input class="all-select-box" type="checkbox" name="all_select_box"/>');
					html_all.push('</li>');
					html_all.push('<li class="order-list-head-content"><span>下注金额</span></li>');
					html_all.push('<li class="order-list-head-content"><span>可赢金额</span></li>');
				html_all.push('</ul>');

				html_all.push('<div class="order-list-content-box"></div>');

			html_all.push('</div>');
		html_all.push('</div>');

		html_all.push('<div class="button-box">');
			//下注/撤单按钮
			html_all.push('<span class="order-submit">确认下单</span>');
			//取消 
			html_all.push('<span class="order-cancel">取消</span>');
		html_all.push('</div>');
	html_all.push('</div>');

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28[defConfig.name] = Main;
})(bomao, bomao.Event);
(function (host, Event, undefined) {

    var pros;
    var defConfig = {
        name: 'miniHistory',
        //父类容器
        UIContainer: '.trend-panel',
        //自身游戏容器
        container: '',
        parentGame: null,
    };

    pros = {
        init: function (cfg) {
            var me = this;
            me.UIContainer = cfg.UIContainer;
            me.parentGame = cfg.parentGame;
            me.container = $('<div class="r"></div>').appendTo(me.parentGame.container.find(me.UIContainer));
            me.container.html(html_all);
            me.data = null,
                me._dxds = 0,
                me._lushu = 0,
                me._move = 0,
                me._move1 = 0,
                // me.arrNumber = [],
                me.newnum = 0;
            //历史开奖数据源
            me.sourceData = null;

            // me.buildUI(c,1,'1');
        },
        //生成二维坐标,参数是数据
        changeArr: function (aa) {

            var me = this;
            var x = 0,
                y = 0,
                ls = [],
                csz = 0,
                a = [
                    [0, 0]
                ],
                _first = aa[0],
                _length = aa.length;

            for (var i = 1; i < aa.length; i++) {

                x = a[a.length - 1][0];   //最后一个数组的X值
                y = a[a.length - 1][1];    //最后一个数组的Y值


                if (aa[i] === _first|| aa[i] === '/' || aa[i] === '?' || aa[i] === '') {
                    if (me.every([x + 1, y], a) && y === csz) {   //判断下一个X轴生成的数组是否有重复，没有就生成它；
                        x += 1;
                        ls = [x, y];
                        if (x > 4) {           //如果超过了4，就让它轴等于前面一个值得X轴；
                            x = a[a.length - 1][0];
                            y += 1;
                            ls = [x, y];
                        }

                    } else {                 //如果下一个X轴生成的有重复，则变化Y轴生成它；
                        y += 1;
                        ls = [x, y];
                        if (x === 0) {
                            csz = y;
                        }
                    }
                    a.push(ls);

                    ls = [];


                } else {
                    csz++;
                    ls = [0, csz];
                    if (!(me.every(ls, a))) {
                        csz++;
                        ls = [0, csz];

                    }
                    a.push(ls);
                    ls = [];
                    _first = aa[i];


                }


            }

            return {_x: x, _y: y, _csz: csz, _a: a, length: _length};
        },
        //用于检验数组是否存在，已经存在返回false，不存在返回True；
        every: function (a, b) {
            var me = this;
            var _a = a,
                _b = b,
                c = true;

            for (var j = 0; j < _b.length; j++) {
                if (me.contrastArr(_b[j], _a) === false) {

                    c = true;//如果对比完了后，发现没有重复，返回true；

                } else {

                    c = false;
                    break;


                }

            }
            return c;
        },
        //判断两个数组是否相等
        contrastArr: function (a, b) {
            var _c = a,
                _d = b,
                c = false;

            for (var k = 0; k < _c.length; k++) {
                if (_c[k] === _d[k]) {
                    c = true;
                } else {
                    c = false;
                    break;
                }
            }
            if (_c.length != _d.length) {
                c = false;
            }
            return c;
        },
        //和值转换成文字,_a参数0是大小，1是单双;_b参数是需要处理的数字，返回大小单双_c;
        changeText: function (_a, _b) {
            var _c = '';
            switch (_a) {
                case 0:
                    if (_b <= 13 && _b >= 0) {
                        _c = '小';
                    } else if (_b > 13) {
                        _c = '大';
                    } else if (_b === '') {
                        _c = '?';
                    } else if (_b === '/') {
                        _c = '/';
                    }
                    break;
                case 1:
                    if (_b % 2 === 0 && _b >= 0) {
                        _c = '双';
                    } else if (_b % 2 != 0) {
                        _c = '单';
                    } else if (_b === '') {
                        _c = '?';
                    } else if (_b === '/') {
                        _c = '/';
                    }
                    break;
            }
            return _c;
        },
        //字符串处理成数字并且相加得出 和值
        changeHezhi: function (_a) {
            var _b = [],
                _c = 0;
            if (_a === '') {
                _c = '';
            } else if (_a == '/') {
                _c = '/';
            } else {
                for (var i = 1; i < 4; i++) {
                    _b.push(parseInt(_a.substring(i, i - 1)));
                }

                for (var j = 0; j < _b.length; j++) {
                    _c = _c + _b[j];
                }
            }

            return _c;
        },
        //生成一维数组，_a代表模式0是大小，1是单双；_b是需要处理的jSON(后端传入);
        creatArr: function (_a, _b) {
            var _c = [''];
            for (var i = 0; i < _b.length; i++) {
                // if(i==_b.length+1){
                // 	_c.push('');
                // }

                if (_b[i].code === '') {
                    _c.push('?');
                } else if (_b[i].code === '/') {
                    _c.push('/');
                } else {
                    var _d = this.changeHezhi(_b[i].code);
                    _c.push(this.changeText(_a, _d));

                }


            }
            _c = _c.reverse();
            return _c;
        },
        //根据数据判断创建多少个li标签,返回数字
        addLi: function (_a, json) {
            var _e = this.creatArr(_a, json),
                _f = this.changeArr(_e)._a,
                _h = _f[0][1];
            for (var i = 1; i < _f.length; i++) {
                if (_h < _f[i][1]) {
                    _h = _f[i][1]
                }
            }
            return _h + 4;
        },
        //创建LI标签,_a参数父层dIV,_b是否路数，_z大小单双,_json是数据
        creatLi: function (_a, _b, _z, json) {
            var _c = '',
                _e = 0,
                _f = '',
                me = this;

            switch (_b) {

                case 0:
                    var _d = this.addLi(_z, json);
                    if (_d < 17) {
                        _d = 17;
                    }
                    for (var i = 0; i < _d; i++) {
                        _e += 1;
                        _c += '<li></li>'
                    }

                    _f = (_d ) * 25 + 'px';
                    me.container.find(_a).find('ul').prepend(_c);
                    me.container.find(_a).css('width', _f);
                    break;
                case 1:

                    var oArr = Math.ceil((this.creatArr(_z, json).length) / 5);
                    if (oArr < 17) {
                        oArr = 17;
                    }
                    for (var i = 0; i < oArr; i++) {
                        _e += 1;
                        _c += '<li></li>'
                    }
                    _f = (oArr) * 25 + 'px';
                    me.container.find(_a).find('ul').prepend(_c);
                    me.container.find(_a).css('width', _f);
                    break;
            }
        },
        getHistory: function (dxds, position, json, l) {

            var me = this;
            me.historyArr = json;
            me.container.find(position).find('ul').empty();
            me.container.find('.ul2-main').css('right','');
            this.creatLi(position, l, dxds, json);

            switch (l) {
                case 0:
                    var _g = this.creatArr(dxds, json),
                        _d = this.changeArr(_g)._a;

                    for (var i = 0; i < _d.length; i++) {
                        var _e = _d[i][0],
                            _f = _d[i][1];

                        if (_g[i] === '小' || _g[i] === '单') {
                            me.container.find(position).find('ul').eq(_e).find('li').eq(_f).addClass('green');
                        } else if (_g[i] === '大' || _g[i] === '双') {
                            me.container.find(position).find('ul').eq(_e).find('li').eq(_f).addClass('red');
                        } else if (_g[i] === '?' || _g[i] === '') {
                            me.container.find(position).find('ul').eq(_e).find('li').eq(_f).addClass('wh');
                        }
                        me.container.find(position).find('ul').eq(_e).find('li').eq(_f).text(_g[i]);
                        // .attr('data-num', me.arrNumber[i])


                    }
                    break;
                case 1:
                    var _csz = 0,
                        _a = this.creatArr(dxds, json),
                        _b = 0;

                    for (var i = 0; i < _a.length; i++) {

                        if (_a[i] === '小' || _a[i] === '单') {
                            me.container.find(position).find('ul').eq(_b).find('li').eq(_csz).addClass('green');
                        } else if (_a[i] === '大' || _a[i] === '双') {

                            me.container.find(position).find('ul').eq(_b).find('li').eq(_csz).addClass('red');
                        } else if (_a[i] === '?' || _a[i] === '') {
                            me.container.find(position).find('ul').eq(_b).find('li').eq(_csz).addClass('wh');
                        }
                        me.container.find(position).find('ul').eq(_b).find('li').eq(_csz).text(_a[i]);
                        _b += 1;
                        if (_b > 4) {
                            _csz += 1;
                            _b = 0;
                        }

                    }

                    break;
            }
            //scroll -floatright
            me.container.find('.ul-2').scrollLeft(parseInt(me.container.find('.ul2-main').css('width')) - 425);


        },
        getInitData: function (data) {
            var me = this;

            me.sourceData = data;
            me.getHistory(me._dxds, me.container.find('.ul2-main'), me.sourceData, me._lushu);
        },
        //更新开奖数据
        updataSourceData: function (newResult) {
            var me = this;

            switch (me.sourceData.length) {
                case 0:

                    me.sourceData.push(newResult);

                    break;
                case 1:

                    if (newResult.code == ''&&me.sourceData[0].number!=newResult.number) {
                        me.sourceData.unshift(newResult);
                    }else if (me.sourceData[0].number == newResult.number) {

                        me.sourceData[0].code = newResult.code;

                    }

                    break;
                default:

                    if (newResult.code == ''&&me.sourceData[0].number!=newResult.number&&me.sourceData[1].number!=newResult.number) {
                        me.sourceData.unshift(newResult);
                    } else {

                        $.each([0, 1], function (i) {
                            if (me.sourceData[i].number == newResult.number) {
                                me.sourceData[i].code = newResult.code;
                            }
                        });
                    }

                    break;
            }

            me.getHistory(me._dxds, me.container.find('.ul2-main'), me.sourceData, me._lushu);

        },

        //跳转官网
        linkOfficePage: function () {
            var me = this;
            me.officeUrl = '';
            switch (me.parentGame.id) {
                case 54:
                    me.officeUrl = 'http://www.cqcp.net';
                    break;
                case 55:
                    me.officeUrl = 'http://www.lottost.cn';
                    break;
                case 56:
                    me.officeUrl = 'http://www.xjflcp.com';
                    break;
                case 57:
                    me.officeUrl = 'http://www.tjflcpw.com';
                    break;
                case 58:
                    me.officeUrl = 'http://www.bwlc.net';
                    break;
                case 59:
                    me.officeUrl = 'http://www.swlc.gov.cn';
                    break;
                default:
                    break;
            }

            window.open(me.officeUrl);
        },
        //刷新走势图
        updataModel: function () {
            var me = this;

            var service = new bomao.Lucky28.DataService();

            service.getAllIssueByGameID(me.parentGame.id, function (data) {
                if (data) {
                    me.sourceData = data;
                    me.getHistory(me._dxds, me.container.find('.ul2-main'), me.sourceData, me._lushu);
                }
            });
        }
    };
    var html_all = '';
    html_all +=
        '<div class="r-a">' +
        '<ul class="ul-1">' +
        '<li class="dx">大小</li>' +
        '<li class="ds">单双</li>' +
        '</ul>' +
        '<div class="ul-2">' +
        '<div class="ul2-main">' +
        '<div class="ul2-l"></div><ul></ul><ul></ul><ul></ul><ul></ul><ul></ul><div class="ul2-r"></div>' +
        '</div>' +
        '</div>' +
        '<ul class="ul-3">' +
        '<li class="ico1"></li>' +
        '<li class="ico2"></li>' +
        '<li class="ico3"></li>' +
        '</ul>' +
        '</div>';


    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;

    host.Lucky28.list[defConfig.name] = Main;
})(bomao, bomao.Event);
(function(host, Event, undefined){
	var defConfig = {
		name:'order',
		UIContainer:'.order-list-content-box',
		container:'',
		bet_value:0,
		win_value:0,
		id:''
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.id=cfg.id;
			me.bet_value=cfg.bet_value;
			me.win_value=cfg.win_value;
			me.UIContainer = $(cfg.UIContainer);
			me.container = $('<ul class="order-list-content" row-list='+me.id+'></ul>').appendTo($(me.UIContainer));
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));
			//更新下注金额 与 可赢金额
			me.container.find('.order-list-bet-value').html(me.bet_value);
			me.container.find('.order-list-win-value').html(me.win_value);
		}
	};

	html_all=[];
	html_all.push('<li class="order-list-head-content">');
		html_all.push('<span>撤单&nbsp;&nbsp;</span>');
		html_all.push('<input class="single-select-box" type="checkbox" name="cancel_order"/>');
	html_all.push('</li>');
	html_all.push('<li class="order-list-head-content"><span class="order-list-bet-value">0.00</span></li>');
	html_all.push('<li class="order-list-head-content"><span class="order-list-win-value">0.00</span></li>');


	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28[defConfig.name] = Main;
})(bomao, bomao.Event);
(function(host, Event, undefined){

	var defConfig = {
		name:'clock',
		//父类容器
		UIContainer:'',
		//自身游戏容器
		container : '',
		cityName:''
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.UIContainer = $(cfg.UIContainer);
			me.cityName = cfg.cityName
			me.container = $('<div class="clock-box"></div>').appendTo(me.UIContainer);
			me.container.html(html_all.join(''));
		},
		updateCityName:function(){
			var me = this;
			me.container.find('.clock-city-name').html(me.cityName);
		},
		updataLeftTime:function(time_distance){
			var me = this;
			
			if(time_distance>=0){
				// 分
				var int_minute = Math.floor(time_distance/60) 
				time_distance -= int_minute * 60; 
				// 秒 
				var int_second = Math.floor(time_distance) 
				// 时分秒为单数时、前面加零 
	
				if(int_minute < 10){ 
					int_minute = "0" + int_minute; 
				} 
				if(int_second < 10){
					int_second = "0" + int_second;
				} 
				// 显示时间
				me.container.find(".time_m_1").html(String(int_minute).substring(0,1));
				me.container.find(".time_m_2").html(String(int_minute).substring(1,2));
				me.container.find(".time_s_1").html(String(int_second).substring(0,1));
				me.container.find(".time_s_2").html(String(int_second).substring(1,2));

			}else{
				if(time_distance == '---'){
					// 显示时间 
					me.container.find(".time_m_1").html("-");
					me.container.find(".time_m_2").html("-");
					me.container.find(".time_s_1").html("-");
					me.container.find(".time_s_2").html("-");
				}else{
					// 显示时间 
					me.container.find(".time_m_1").html("0");
					me.container.find(".time_m_2").html("0");
					me.container.find(".time_s_1").html("0");
					me.container.find(".time_s_2").html("0");
				}
			
			}
		}
	};

	//html模板
	var html_all = [];

	html_all.push('<span class="clock-city-name"></span>');
	html_all.push('<span class="clock-city-time">');
		html_all.push('<span class="clock-time-txt time_m_1"></span>');
		html_all.push('<span class="clock-time-txt time_m_2"></span>');
		html_all.push('<span class="clock-time-txt time_s_1"></span>');
		html_all.push('<span class="clock-time-txt time_s_2"></span>');
	html_all.push('</span>');

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28[defConfig.name] = Main;
})(bomao, bomao.Event);
(function(host, Event, undefined){
	var defConfig = {
		name:'resultRecord',
		container:'',
		UIContainer:'.record-panel'
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.UIContainer = cfg.UIContainer;
			me.container = $('<div class="r-allday"></div>').appendTo($(me.UIContainer));
			me.container.html(html_all);
	// console.log("xxx");
		}
	};

	var html_all='';
	html_all+= '<div class="r-close close-order"></div>'+
		'    <div class="r-main">'+
		'        <div class="main-top">历史记录（日汇总）</div>'+
		'<ul class="main-mid">'+
		'    <li class="mid-1">日期：</li>'+
		'    <li class="mid-2">'+
		'        <input type="text" readonly id="r-allday"><i></i>'+
		'    </li>'+
		'    <li class="mid-3">至</li>'+
		'    <li class="mid-4">'+
		'        <input type="text" readonly id="r-alldayto"/>'+
		'    </li>'+
		'    <li class="mid-5" id="today">今日</li>'+
		'    <li class="mid-6" id="week">本周</li>'+
		'    <li class="mid-7" id="3day">近三日</li>'+
		'    <li class="mid-8" id="hmonth">近半月</li>'+
		'    <li class="six">'+
		'    </li>'+
		'</ul>'+
		'        <div class="main-bot">'+
		'            <div class="bot-l"><div class="top">'+
		'                <ul class="title">'+
		'                    <li class="num-1">投注日期</li>'+
		'                    <li class="num-2">有效投注</li>'+
		'                    <li class="num-3">派奖金额</li>'+
		'                    <li class="num-4">盈亏金额</li>'+
		'                    <li class="num-5">操作</li>'+
		'                </ul></div><div class="bottom"> </div>'+
		'            </div>'+
		'        </div>'+
		'</div>';

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28[defConfig.name] = Main;

})(bomao, bomao.Event);
(function(host, Event, undefined){
	var defConfig = {
		name:'historyRecord',
		container:'',
		UIContainer:'.record-panel',
		initData:null
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.UIContainer = cfg.UIContainer;
			me.container = $('<div class="r-history"></div>').appendTo($(me.UIContainer));
			me.initData = cfg.initData;
			// console.log(me.initData);
			me.nameArr = [];
			me.nameId = [];
			// me.container.html(html_all);
			me.initNameArray();
			me.updateGameNameList();
			// console.log(me.nameArr);
		},
		initNameArray:function () {
			var me=this;
			// console.log(me.initData);
			$.each(me.initData , function(i , data){
				// console.log(data);
				me.nameArr.push(data.gameName_cn);
				me.nameId.push(data.gameId);
			});
		},
		updateGameNameList:function(){
			var me =this;
			$.each(me.nameArr,function (i) {
				optionStr+='<option name="lottery_id='+me.nameId[i]+'">'+me.nameArr[i]+'</option>';
			});
			me.container.html(html_part_1+'<option name="all">所有游戏</option>'+optionStr+html_part_2);

		}

	};

	var optionStr='';

	var html_part_1 = '<div class="r-close close-order"></div>'+
		'    <div class="r-a">'+
		'        <div class="a-1">'+
		'            <h5>历史明细</h5>'+
		'            <span id="r-return"></span>'+
		'        </div>'+
		'        <ul class="a-2">'+
		'            <li class="r-cp">彩票：</li>'+
		'            <li>'+
		'                <select id="all-cp" class="all-cp">';

	var html_part_2 = '                </select>'+
		'            </li>'+
		'            <li>'+
		'                状态:'+
		'            </li>'+
		'            <li>'+
		'               <label class="check" for="r-all"><input type="checkbox" id="r-all" name="series_id=20"/></label>'+
		'                全选'+
		'            </li>'+
		'            <li>'+
		'                <label class="check" for="status0"><input type="checkbox" id="status0" name="status_0=0"/></label>'+
		'                有效投注(未开奖)'+
		'            </li>'+
		'            <li>'+
		'                <label class="check" for="status1"><input type="checkbox" id="status1" name="status_1=1"/></label>'+
		'                已撤单'+
		'            </li>'+
		'            <li>'+
		'                <label class="check" for="status2"><input type="checkbox" id="status2" name="status_2=2"/></label>'+
		'                未中奖'+
		'            </li>'+
		'            <li>'+
		'                <label class="check" for="status3"><input type="checkbox" id="status3" name="status_3=3"/></label>'+
		'                已中奖'+
		'            </li>'+
		'            <li class="last" id="r-search"></li>'+
		'        </ul>'+
		'        <div class="a-3">'+
		'           <ul class="a3-1">'+
		'               <li class="num-1">投注时间</li>'+
		'               <li class="num-3">游戏</li>'+
		'               <li class="num-4">编号</li>'+
		'               <li class="num-5">奖期No.</li>'+
		'               <li class="num-6">投注内容</li>'+
		'               <li class="num-7">投注额</li>'+
		'               <li class="num-8">中奖金额</li>'+
		'               <li class="num-9">状态</li>'+
		'               <li class="num-10">操作</li>'+
		'           </ul>'+
		'            <div class="a3-2">'+
		'                <div class="l">'+
		'                </div>'+
		'            </div>'+
		'        </div>'+
		'</div>';



	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28[defConfig.name] = Main;
})(bomao, bomao.Event);
(function(host, Event, undefined){
	var defConfig = {
		name:'Awardforlottery',
		container:'',
		UIContainer:'.record-panel',
		initData:null
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.UIContainer = cfg.UIContainer;
			me.container = $('<div class="r-kjjg"></div>').appendTo($(me.UIContainer));
			me.initData = cfg.initData;
			// console.log(me.initData);
			me.nameArr = [];
			me.nameId = [];
			// me.container.html(html_all);
			me.initNameArray();
			me.updateGameNameList();
			// console.log(me.nameArr);
		},
		initNameArray:function () {
			var me=this;
			// console.log(me.initData);
			$.each(me.initData , function(i , data){
				// console.log(data);
				me.nameArr.push(data.gameName_cn);
				me.nameId.push(data.gameId);
			});
		},
		updateGameNameList:function(){
			var me =this;
			$.each(me.nameArr,function (i) {
				optionStr+='<option name="lottery_id='+me.nameId[i]+'">'+me.nameArr[i]+'</option>';
			});
			me.container.html(html_part_1+optionStr+html_part_2);

		}

	};

	var optionStr='';

	var html_part_1 =
		'<div class="r-close close-order"></div>'+
		'    <div class="r-main">'+
		'        <div class="main-top">开奖记录</div>'+
		'        <ul class="main-mid">'+
		'            <li>'+
		'                <h5>彩票：</h5>'+
		'                <select id="kjjg-select" class="all-cp">';

	var html_part_2 =
		'                </select>'+
		'            </li>'+
		'            <li class="one">'+
		'                <h5>日期：</h5>'+
		'                <input type="text" readonly id=\'r-kjjg\' value=""/>' +
		'					<i></i>'+
		'            </li>'+
		'            <li>'+
		'                <h5>奖期：</h5>'+
		'                <input name="r-jiangqi" type="text" class="r-kjjq"/>'+
		'            </li>'+
		'            <!--<li>今日</li>-->'+
		'            <!--<li>本周</li>-->'+
		'            <!--<li>近三日</li>-->'+
		'            <!--<li>近半月</li>-->'+
		'            <li class="six"></li>'+
		'        </ul>'+
		'        <div class="main-bot">'+
		'            <div class="bot-l">' +
						'<div class="top">'+
			'                <ul class="title">'+
			'                    <li>期号</li>'+
			'                    <li>开奖时间</li>'+
			'                    <li>结果</li>'+
			'                    <li>和</li>'+
			'                    <li>大小</li>'+
			'                    <li>单双</li>'+
			'                    <li>二极</li>'+

			'                </ul>' +
						'</div>' +
		'				<div class="bottom"><div class="bottom-a"></div>' +
		'				</div>'+
		'            </div>'+
		'        </div>'+
		'</div>';



	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28[defConfig.name] = Main;
})(bomao, bomao.Event);
var gameMethods = {};

var analysisData = function (id, name) {

    var gameMethodConfig = {
        data: {},
        getMethodConfigByName: function (name) {
            return this.data[name];
        }
    };

    var gameConfigData = name;
    gameMethodConfig.id = id;

    $.each(gameConfigData, function () {
        var data = gameConfigData['gameMethods'],
            nodeCache = {},
            methodCache = {},
            node1,
            node2,
            node3;

        $.each(data, function () {
            node1 = this;
            node1['fullname_en'] = [node1['name_en']];
            node1['fullname_cn'] = [node1['name_cn']];
            nodeCache['' + node1['id']] = node1;

            if (node1['children']) {
                $.each(node1['children'], function () {
                    node2 = this;
                    node2['fullname_en'] = node1['fullname_en'].concat(node2['name_en']);
                    node2['fullname_cn'] = node1['fullname_cn'].concat(node2['name_cn']);
                    nodeCache['' + node2['id']] = node2;

                    if (node2['children']) {
                        $.each(node2['children'], function () {
                            node3 = this;
                            node3['fullname_en'] = node2['fullname_en'].concat(node3['name_en']);
                            node3['fullname_cn'] = node2['fullname_cn'].concat(node3['name_cn']);
                            gameMethodConfig.data[node3['fullname_en'].join('-')] = node3;
                        });
                    }
                });
            }
        });
    });

    gameMethods[id] = gameMethodConfig;
};

(function (host) {
    //获取浏览器信息
    function getBrowserInfo() {
        var Sys = {};
        var ua = navigator.userAgent.toLowerCase();
        var s; (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
            (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
            (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
            (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
            (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;

        if(Sys.ie) {
            return Sys.ie;
        }
    }

    var browser = getBrowserInfo() ;
    //var verinfo = (browser+"").replace(/[^0-9.]/ig, "");      // 版本号
    
    //ie版本低于ie10
    if(Number(browser)<Number('10.0')){
        $('.happy-panel').html('');
        $('.happy-panel').hide();
        $('.ie-tips').show();

        var downloadUrl = '';

        $('.browser-box').on('click','.browser-lab' , function(){
            var index = $(this).attr('param');
            switch(index){
                case '0': downloadUrl='https://www.google.com/chrome/browser/desktop/index.html';break;
                case '1': downloadUrl='http://se.360.cn/';break;
                case '2': downloadUrl='http://www.firefox.com.cn/';break;
                case '3': downloadUrl='https://liulanqi.baidu.com/';break;
                case '4': downloadUrl='http://www.maxthon.cn/';break;
                default:break;
            };

            window.open(downloadUrl);
        });
    }
})(bomao);

//游戏数组
var gameArray = [];

(function (host) {

    for (var i in global_game_config_lucky28.gameInfo) {
        analysisData(global_game_config_lucky28.gameInfo[i].gameId, global_game_config_lucky28.gameInfo[i]);
    }

    //监听屏幕宽度
    window.onresize = function () {
        if (document.body.clientWidth > 1280) {
            $('#clockList').css({
                display: 'block'
            });
        }
    };
    
    //左侧时钟列表，位置调整
    $(window).scroll(function(){
        if($(window).scrollTop()>=124){
            $(".clock-slider").addClass('clock-slider-active');
            $(".informationList").addClass('informationList-active');
        }else{
            $(".clock-slider").removeClass('clock-slider-active');
            $(".informationList").removeClass('informationList-active');
        }
    });

})(bomao);

(function (host) {
    var service = new bomao.Lucky28.DataService();

    //游戏名称 ，游戏ID ， 游戏父容器  ， 游戏赔率设定 ， 游戏新奖期数据 ， 游戏初始化奖期数据
    for (var i in global_game_config_lucky28.gameInfo) {

        var gameName = global_game_config_lucky28.gameInfo[i].gameName_cn.substring(0, global_game_config_lucky28.gameInfo[i].gameName_cn.length - 4);
        var gameUIContainer = '#' + global_game_config_lucky28.gameInfo[i].gameName_en;

        creatGame(
            gameName,
            global_game_config_lucky28.gameInfo[i].gameId,
            gameUIContainer,
            gameMethods[global_game_config_lucky28.gameInfo[i].gameId],
            global_game_config_lucky28,
            global_game_init_data_lucky28[global_game_config_lucky28.gameInfo[i].gameId]
        );

    }

    $('#clockList').css({
        height: $('.lucky-main-panel').height()
    });


    var order_window = new bomao.Lucky28.orderWindow({service: service});

    //只允许输入数字
    order_window.container.find(".money-box").bind('input propertychange', function () {
        var box_value = order_window.container.find(".money-box").val();
        order_window.container.find(".money-box").val(box_value.replace(/[^\d]/g, ''));

        if (box_value.charAt(0) == 0) {
            order_window.container.find(".money-box").val('');
        }

        //兼容ie判断
        if(order_window.cell_data){
            if (Number(box_value) > (order_window.cell_data.extra-order_window.bet_value_total)) {
                order_window.container.find(".money-box").val((order_window.cell_data.extra-order_window.bet_value_total));
            }
        }
        
        //限制输入金额为最大账户金额
        if (Number(box_value) > parseInt(order_window.userAccount)) {
            order_window.container.find(".money-box").val(parseInt(order_window.userAccount));
        }
    });
    //处于焦点时，清空内容
    order_window.container.on('focus', '.money-box', function () {
        if ($(this).val() == '请输入下注金额') {
            $(this).val('');
        }
    });
    //离开输入框，若为空。
    order_window.container.on('blur', '.money-box', function () {
        if ($(this).val() == '') {
            $(this).val('请输入下注金额');
        }
    });
    //点击1 2 5 10 50 100 500金额输入框
    order_window.container.on('mousedown', '.money-list li', function () {
        var money_value = $(this).attr('param');

        if(money_value == "all"){
            $('.money-box').val((order_window.cell_data.extra-order_window.bet_value_total));
        }else{
             //下注金额不能超过限额
            if(money_value>(order_window.cell_data.extra-order_window.bet_value_total)){
                $('.money-box').val((order_window.cell_data.extra-order_window.bet_value_total));
            }else{
                $('.money-box').val(money_value);
            }
        }

        if ($(this).attr('param')) {
            $(this).addClass('money-list-box-active');
        }
    });
    order_window.container.on('mouseup', '.money-list li', function () {
        $('.money-box').focus();
        var money_value = $(this).attr('param');

        if(money_value == "all"){
            $('.money-box').val((order_window.cell_data.extra-order_window.bet_value_total));
        }else{
             //下注金额不能超过限额
            if(money_value>(order_window.cell_data.extra-order_window.bet_value_total)){
                $('.money-box').val((order_window.cell_data.extra-order_window.bet_value_total));
            }else{
                $('.money-box').val(money_value);
            }
        }
        
        $(this).removeClass('money-list-box-active');
    });
    order_window.container.on('mouseleave', '.money-list li', function () {
        $(this).removeClass('money-list-box-active');
    });
    //关闭订单菜单
    order_window.container.on('click', '.close-order', function () {
        order_window.closeOrder();
    });
    //监听下单窗口的菜单切换
    order_window.container.on('click', '.tag-menu', function () {
        var index = $(this).attr('param');
        order_window.switchMenu(index);
    });
    //点击全选框
    order_window.container.on('click', '.all-select-box', function () {
        $("input[name='cancel_order']").prop("checked", $("input[name='all_select_box']").is(':checked'));

        if ($("input[name='all_select_box']").is(':checked')) {
            order_window.cur_cancel_orders_arr = [];
            for (var i in order_window.cur_orders_arr) {
                order_window.cur_cancel_orders_arr.push(order_window.cur_orders_arr[i].id);
            }
        } else {
            order_window.cur_cancel_orders_arr = [];
        }
    });
    //点击单个订单
    order_window.container.on('click', 'input[name="cancel_order"]', function () {
        var orderId = Number($(this).parent().parent().attr("row-list"));
        if ($(this).is(':checked')) {
            if (order_window.cur_cancel_orders_arr.indexOf(orderId) == -1) {
                order_window.cur_cancel_orders_arr.push(orderId);
            }
        } else {
            if (order_window.cur_cancel_orders_arr.indexOf(orderId) != -1) {
                order_window.cur_cancel_orders_arr.splice(order_window.cur_cancel_orders_arr.indexOf(orderId), 1);
            }

            $("input[name='all_select_box']").attr("checked",false);
        }
    });
    //订单-确定按钮效果
    order_window.container.on('mousedown', '.order-submit', function () {
        $(this).addClass('order-button-active');

        if (order_window.current_menu == 0) {
            order_window.submitOrder();
        } else {
            order_window.submitCancelOrder();
        }
    });
    order_window.container.on('mouseup', '.order-submit', function () {
        $(this).removeClass('order-button-active');
    });
    order_window.container.on('mouseleave', '.order-submit', function () {
        $(this).removeClass('order-button-active');
    });
    //确认下注，增加回车监听
    $(document).keydown(function(e){
        var key = e.which; //e.which是按键的值
        if(key == 13 && order_window.container.hasClass('order-panel-show')){
            if (order_window.current_menu == 0) {
                order_window.submitOrder();
            }
        };
    });
    //订单-取消按钮效果
    order_window.container.on('mousedown', '.order-cancel', function () {
        $(this).addClass('order-button-active');
        if (order_window.current_menu == 0) {
            order_window.cancelOrder();
        } else {
            order_window.cancelSelectOrder();
        }
    });

    order_window.container.on('mouseup', '.order-cancel', function () {
        $(this).removeClass('order-button-active');
    });

    order_window.container.on('mouseleave', '.order-cancel', function () {
        $(this).removeClass('order-button-active');
    });
    //订单拖动效果
    order_window.container.on('mousedown', '.order-menu-head', function (event) {
        var e = event || window.event;
        order_window.drag = true;
        order_window._x = e.pageX - order_window.container.position().left;
        order_window._y = e.pageY - order_window.container.position().top;
    });

    order_window.container.on('mousemove', '.order-menu-head', function (event) {
        if (!order_window.drag) {
            return false;
        }
        var e = event || window.event;
        //当前可视区域的坐标
        var x = e.pageX - order_window._x;
        var y = e.pageY - order_window._y;

        var minL = $(".lucky-main-panel").offset().left;
        var maxL = $(".lucky-main-panel").offset().left + $(".lucky-main-panel").width() - order_window.container.outerWidth();

        var minT = $(".lucky-main-panel").offset().top > $(window).scrollTop() ? $(".lucky-main-panel").offset().top - $(window).scrollTop() : 0;
        var maxT = $(".lucky-main-panel").height() - ($(window).scrollTop() - $(".lucky-main-panel").offset().top) - order_window.container.outerHeight();

        x = x < minL ? minL : x;
        x = x > maxL ? maxL : x;

        y = y < minT ? minT : y;
        y = y > maxT ? maxT : y;

        order_window.container.css({
            left: x,
            top: y,
        });
    });
    order_window.container.on('mouseup', '.order-menu-head', function (event) {
        order_window.drag = false;
    });

    order_window.container.on('mouseleave', '.order-menu-head', function (event) {
        order_window.drag = false;
    });

    $(window).scroll(function (event) {
        if (order_window.container.offset().top - $(".lucky-main-panel").offset().top < 0) {
            order_window.container.css({
                top: $(".lucky-main-panel").offset().top - $(window).scrollTop(),
            });
        }
        if (order_window.container.offset().top - $(".lucky-main-panel").offset().top > $(".lucky-main-panel").height() - order_window.container.outerHeight()) {
            if ($(".lucky-main-panel").height() + $(".lucky-main-panel").offset().top - order_window.container.outerHeight() > $(window).scrollTop()) {
                order_window.container.css({
                    top: $(".lucky-main-panel").height() + $(".lucky-main-panel").offset().top - order_window.container.outerHeight() - $(window).scrollTop(),
                });
            }
        }
    });

    var historyRecord = new bomao.Lucky28.historyRecord({'initData':global_game_config_lucky28.gameInfo});

    var resultRecord = new bomao.Lucky28.resultRecord();

    var Awardforlottery = new bomao.Lucky28.Awardforlottery({'initData':global_game_config_lucky28.gameInfo});




    (function () {

      function checkdate(id){
          // alert(id);
                switch(id){
                    case 'today':
                        startDate = now;
                        endDate =   now;
                        break;
                    case 'week':
                        startDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek);
                        endDate = new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek));
                        break;
                    case 'month':
                        startDate = new Date(nowYear, nowMonth, 1);
                        endDate = new Date(nowYear, nowMonth, getMonthDays(nowMonth));
                        break;
                    case '3day':
                        startDate =new Date(now.getTime() -3*24*3600*1000);
                        endDate =   now;
                        break;
                    case 'hmonth':
                        startDate =new Date(now.getTime() -15*24*3600*1000);
                        endDate =   now;
                        break;
                    case '1month':
                        startDate =new Date(now.getTime() -30*24*3600*1000);
                        endDate =   now;
                        break;
                }
          // alert(formatDate(startDate));
                // document.getElementById('J-date-start').value=formatDate(startDate)+ ' 00:00:00';
               $('#r-allday').val(formatDate(startDate));
                $('#r-alldayto').val(formatDate(endDate));
            }
        var now = new Date(); //当前日期
        var nowDayOfWeek = now.getDay(); //今天本周的第几天
        var nowDay = now.getDate(); //当前日
        var nowMonth = now.getMonth(); //当前月
        var nowYear = now.getYear(); //当前年
        nowYear += (nowYear < 2000) ? 1900 : 0; //
        var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek);

        function formatDate(date) {
            var myyear = date.getFullYear();
            var mymonth = date.getMonth()+1;
            var myweekday = date.getDate();

            if(mymonth < 10){
                mymonth = "0" + mymonth;
            }
            if(myweekday < 10){
                myweekday = "0" + myweekday;
            }
            return (myyear+"-"+mymonth + "-" + myweekday);
        }
        function getMonthDays(myMonth){
            var monthStartDate = new Date(nowYear, myMonth, 1);
            var monthEndDate = new Date(nowYear, myMonth + 1, 1);
            var days = (monthEndDate - monthStartDate)/(1000 * 60 * 60 * 24);
            return days;
        }
        $('#r-allday').focus(function () {
            (new bomao.DatePicker({input: '#r-allday', isShowTime: false, startYear: 2013})).show();
        });
        $('#r-alldayto').focus(function () {
            (new bomao.DatePicker({input: '#r-alldayto', isShowTime: false, startYear: 2013})).show();
        });
        $('#r-kjjg').focus(function () {
            (new bomao.DatePicker({input: '#r-kjjg', isShowTime: false, startYear: 2013})).show();
        });

        $('.r-allday #today,.r-allday #week,.r-allday #3day,.r-allday #hmonth').on('click',function () {
            // alert(1);
                var _a = $(this).attr('id');
            checkdate(_a);
        });



//         $('#r-allday').daterangepicker(
//             {
//                 startDate: moment().subtract(1, 'days').startOf('day'),
//                 autoApply: true,
//                 endDate: moment(),
//                 minDate: '01/01/2016',  //最小时间
//                 maxDate: moment(), //最大时间
//                 dateLimit: {
//                     days: 365
//                 }, //起止时间的最大间隔
//                 showDropdowns: true,
//                 showWeekNumbers: false, //是否显示第几周
// //                  timePicker : true, //是否显示小时和分钟
//                 timePickerIncrement: 60, //时间的增量，单位为分钟
// //                  timePicker12Hour : false, //是否使用12小时制来显示时间
//                 ranges: {
//                     //'最近1小时': [moment().subtract('hours',1), moment()],
//                     '今日': [moment().startOf('day'), moment()],
//                     '昨日': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
//                     '最近7日': [moment().subtract(6, 'days'), moment()],
//                     '最近30日': [moment().subtract(29, 'days'), moment()]
//                 },
//                 opens: 'right', //日期选择框的弹出位置
//                 buttonClasses: ['btn btn-default'],
//                 applyClass: 'btn-small btn-primary blue',
//                 cancelClass: 'btn-small',
//                 separator: ' to ',
//                 locale: {
//                     format: 'YYYY-MM-DD', //控件中from和to 显示的日期格式
//                     separator: "/",
//                     applyLabel: '确定',
//                     cancelLabel: '取消',
//                     fromLabel: '起始时间',
//                     toLabel: '结束时间',
//                     customRangeLabel: '自定义',
//                     daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
//                     monthNames: ['一月', '二月', '三月', '四月', '五月', '六月',
//                         '七月', '八月', '九月', '十月', '十一月', '十二月'],
//                     firstDay: 1
//                 },
//                 linkedCalendars: false,
//                 alwaysShowCalendars: true
//
//             });
//         $('#r-kjjg').daterangepicker(
//             {
//                 startDate: moment().startOf('day'),
//                 autoApply: true,
//                 endDate: moment(),
//                 minDate: '01/01/2016',	//最小时间
//                 maxDate: moment(), //最大时间
//                 dateLimit: {
//                     days: 365
//                 }, //起止时间的最大间隔
//                 showDropdowns: true,
//                 showWeekNumbers: false, //是否显示第几周
// //                  timePicker : true, //是否显示小时和分钟
//                 timePickerIncrement: 60, //时间的增量，单位为分钟
// //                  timePicker12Hour : false, //是否使用12小时制来显示时间
//                 ranges: {
//                     //'最近1小时': [moment().subtract('hours',1), moment()],
//                     '今日': [moment().startOf('day'), moment()],
//                     '昨日': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
//                     '最近7日': [moment().subtract(6, 'days'), moment()],
//                     '最近30日': [moment().subtract(29, 'days'), moment()]
//                 },
//                 opens: 'right', //日期选择框的弹出位置
//                 buttonClasses: ['btn btn-default'],
//                 applyClass: 'btn-small btn-primary blue',
//                 cancelClass: 'btn-small',
//                 separator: ' to ',
//                 locale: {
//                     format: 'YYYY-MM-DD', //控件中from和to 显示的日期格式
//                     separator: "/",
//                     applyLabel: '确定',
//                     cancelLabel: '取消',
//                     fromLabel: '起始时间',
//                     toLabel: '结束时间',
//                     customRangeLabel: '自定义',
//                     daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
//                     monthNames: ['一月', '二月', '三月', '四月', '五月', '六月',
//                         '七月', '八月', '九月', '十月', '十一月', '十二月'],
//                     firstDay: 1
//                 },
//                 singleDatePicker: true
//
//             });

        // caizhong);
        var _address = "/projects/mini-window-xy28?series_id=20",
            _b = 1,
            _c = $('.r-history .a3-2').height(),
            _lsadd = '',
            _kjjg = '/bets/wnnumber-result?',
            _kjjgdiv = $('.r-kjjg .main-bot .bottom').height(),
            _lskjjg = '';

        function csh() {
            _b = 1;
            _c = 433;
            // $('.r-history .a-3 .a3-2').scrollTop(0);
            // $('.r-kjjg .main-bot').scrollTop(0);
            $('html').removeAttr('style');
            // alert(1);
        };

        function address_clean() {
            _address = "/projects/mini-window-xy28?series_id=20";
            _lsadd = '';
        }

        function unchecked() {
            $('.a-2 li input').attr('checked', false);
        }

        function unoption() {
            $('#all-cp option').attr('selected', false);
            $('#all-cp option:eq(0)').attr('selected', true);

        }

        function rZZ(onOff) {
            if (onOff) {
                $('body').append('<div class="r-zz"></div>');
            } else {
                $('body .r-zz').remove();
            }

        }
        function cztoid(cz) {
            var _a =1;

            switch(cz)
            {
                case '重庆快乐28':
                    _a=54;
                    break;
                case '黑龙江快乐28':
                    _a=55;
                    break;
                case '新疆快乐28':
                    _a=56;
                    break;
                case '天津快乐28':
                    _a=57;
                    break;
                case '北京快乐28':
                    _a=58;
                    break;
                case '上海快乐28':
                    _a=59;
                    break;
            }

            return _a;
        }

        $(document)
            .on('click', '.r-allday .six', function () {
                var _address = '/bets/profits/20/',
                    _choose = $('#r-allday').val()+'/'+$('#r-alldayto').val();
                
                $.ajax({
                    url: _address += _choose,
                    cache: false,
                    success: function (html) {
                        if(html=='[]'){
                            html='<div class="r-error">您查询的条目不存在，请重试</div>'
                            $('.r-allday .bot-l .bottom').empty().append(html);
                        }else {
                            var _a = JSON.parse(html),
                                _lenght = _a.length,
                                _all = '';
                            $.each(_a, function (i) {
                                _all += '<ul>' +
                                    '<li class="num-1">' + _a[i][0] + '</li>' +
                                    '<li class="num-2">' + _a[i][1] + '</li>' +
                                    '<li class="num-3">' + _a[i][2] + '</li>' +
                                    '<li class="num-4">' + _a[i][3] + '</li>' +
                                    '<li class="r-xq num-5"></li>' +
                                    '</ul>';
                            })
                            $('.r-allday .bot-l .bottom').empty().append(_all);
                        }




                    }
                });
            })
            .on('click', '.r-allday .r-close', function () {
                $('html').removeAttr('style');
                unchecked();
                csh();
                address_clean();
                rZZ(false);
                $('.r-allday').fadeOut();


            })
            .on('click', '.r-history .r-close', function () {

                unchecked();
                csh();
                address_clean();
                rZZ(false);
                $('.r-history').fadeOut();

            })
            .on('click', '.r-kjjg .r-close', function () {

                unchecked();
                csh();
                address_clean();
                rZZ(false);
                $('.r-kjjg').fadeOut();
                $('#kjjg-select option:eq(0)').attr('selected', true);

            })
            .on('click', '.r-introduce .r-close', function () {
                $('.r-introduce').hide();
                $('html,body').removeAttr('style');
                rZZ(false);
            })
            .on('click', '.historyButton', function () {
                $('body,html').scrollTop(0);
                $('html').css('overflow-y', 'hidden');
                rZZ(true);
                $.ajax({
                    url: "/bets/profits/20/",
                    cache: false,
                    success: function (html) {
                        if(html=='[]'){
                            html = '<div class="r-error">暂时没有投注记录</div>'
                            $('.r-allday .bot-l .bottom').empty().append(html);
                        }else {
                            var _a = JSON.parse(html),
                                _lenght = _a.length,
                                _all = '';
                            $.each(_a, function (i) {
                                _all += '<ul>' +
                                    '<li class="num-1">' + _a[i][0] + '</li>' +
                                    '<li class="num-2">' + _a[i][1] + '</li>' +
                                    '<li class="num-3">' + _a[i][2] + '</li>' +
                                    '<li class="num-4">' + _a[i][3] + '</li>' +
                                    '<li class="r-xq num-5"></li>' +
                                    '</ul>';
                            })
                            $('.r-allday .bot-l .bottom').empty().append(_all);
                        }
                    }
                });

                // http://bocat.user/bets/wnnumber-result
                $('.r-allday').fadeIn();
                $('.r-kjjg').hide();

            })
            .on('click', '.resultButton', function () {
                $('body,html').scrollTop(0);
                $('html').css('overflow-y', 'hidden');
                rZZ(true);

                $.ajax({
                    url: "/bets/wnnumber-result",
                    cache: false,
                    success: function (html) {
                        $('.r-kjjg .main-bot .bot-l .bottom-a').empty().append(html);
                        $('.r-kjjg').fadeIn();
                        $('.r-allday').fadeOut();
                        $('.r-history').fadeOut();

                    }
                });

            })
            .on('click', '#r-search', function () {

                $('.r-history .a-3 .a3-2').scrollTop(0);
                _b = 1;
                _c = 433;
                var _a = $('#all-cp').select(),
                    _b = [],
                    _blength = 0;
                _lsadd = _address;

                $('.a-2 input:checkbox:checked').each(function (i) {
                    if ($(this).attr('name') != 'allselect') {
                        _b.push($(this).attr('name'));
                    }
                });
                $.each(_b, function (i) {
                    _lsadd += '&' + _b[i];
                });
                if ($('#all-cp').val() != '所有游戏') {
                    var _cpName = $("#all-cp").find('option:selected').attr('name');
                    _lsadd += '&' + _cpName;
                }
                $.ajax({
                    url: _lsadd,
                    cache: false,
                    success: function (html) {
                        if (html == '') {
                            html = '<div class="r-error">抱歉，您查询的条目暂时没有数据</ul>'
                        }
                        $('.r-history .a3-2 .l').empty().append(html);
                        $('.r-history').fadeIn();
                        $('html').css('overflow-y', 'hidden');

                    }
                });
            })
            .on('click', '.r-xq', function () {
                var _a = $(this).siblings('li:eq(0)').text(),
                // var _a = '2016-06-30',
                    _b = ' 00:00:00',
                    _c = ' 23:59:59',
                    _d = '/projects/mini-window-xy28?series_id=20&bought_at_from=' + _a + _b + '&bought_at_to=' + _a + _c;
                _address = _d;

                $.ajax({
                    url: _d,
                    cache: false,
                    success: function (html) {
                        if (html == '') {
                            html = "<div class='r-error'>当天没有投注数据，请返回查看其他日期</ul>"
                        }
                        $('.r-history .a3-2 .l').empty().append(html);
                    }
                });
                $('.r-allday').fadeOut();
                $('.r-history').fadeIn();
            })
            .on('click', '#r-return', function () {
                _b = 1;
                _c = 433;
                unchecked();
                unoption();
                $('.r-history').fadeOut();
                $('.r-allday').fadeIn();
            })
            .on('click', '.r-history .a-2 li input[name="series_id=20"]', function () {
                if ($(this).is(':checked')) {
                    $(this).parent().parent().siblings('li').find('input').prop('checked', true);
                    $(this).parent().parent().siblings('li').find('label').removeClass('check').addClass('checkin');
                } else {
                    $(this).parent().parent().siblings('li').find('input').prop('checked', false);
                    $(this).parent().parent().siblings('li').find('label').removeClass('checkin').addClass('check');
                }
            })
            // .on('click', '.a-2 li label', function () {
            //     alert(1);
            //
            // })
            .on('click', '.r-kjjg .six', function () {
                _b = 1;
                _c = 433;
                $('.r-kjjg .main-bot').scrollTop(0);
                var _choose = 'date=' + $('#r-kjjg').val(),
                    _text = $('.r-kjjg .main-mid input[name=r-jiangqi]').val().replace(/\s/g, ""),
                    _true = false;


                if (_text != '') {
                    _choose += '&issue=' + _text;
                    _true = true;
                }

                if ($('#kjjg-select').val() != '所有游戏') {
                    var _cpName = $("#kjjg-select").find('option:selected').attr('name');

                    _choose += '&' + _cpName;
                }
                $.ajax({
                    url: _kjjg + _choose,

                    cache: false,
                    success: function (html) {
                        if (html == '') {
                            html = '<div class="r-error">抱歉，您查询的条目暂时没有数据</ul>'
                        }
                        $('.r-kjjg .main-bot .bot-l .bottom-a').empty();
                        // $('.r-kjjg .main-bot .bot-l ul[class!="title"]').remove();
                        // $('.r-kjjg .main-bot .bot-l div').remove();
                        if (_true) {
                            // $('.r-kjjg .main-bot .bot-l').append(html);
                            // alert(typeof html);
                            // alert(html);
                            var _aArr = html.split("</ul>");
                            $('.r-kjjg .main-bot .bot-l .bottom-a').append(_aArr[0] + '</ul>');
                        } else {
                            $('.r-kjjg .main-bot .bot-l .bottom-a').append(html);
                        }

                    }
                });

                _lskjjg = _kjjg + _choose;
            })
            .on('click', '.r-zz', function () {
                $('.r-allday').hide();
                $('.r-history').hide();
                $('.r-kjjg').hide();
                $('.r-introduce').hide();
                $('html,body').removeAttr('style');
                $(this).remove();
            })
            .on('click', '.r-history .num-10 .confirm', function () {
                // var _a ='http://bocat.user/projects/9960702/drop/0',

                var _a = $(this).attr('value'),
                    me = this,
                    _c = $(me).parent().parent('ul'),
                    _idnum = cztoid($.trim($(me).parent().parent().find('.num-3').text())),
                    _jqnum = $.trim($(me).parent().parent().find('.num-5').text()),
                    _jqid=0;

                $.ajax({
                    url: _a,
                    cache: false,
                    success: function (html) {
                        var _b = JSON.parse(html);
                        if (_b.isSuccess == 1) {
                            // alert('恭喜撤单成功!')
                            _c.hide();
                            _c.attr('style', '');
                            _c.find('.num-9').text('已撤销');
                            _c.find('.num-10 div').remove();
                            _c.show();

                            $.each(gameArray,function (i) {
                                if(gameArray[i].id==_idnum){
                                    _jqid = i;
                                }
                            });

                            service.getOrders(_idnum , function(data){
                                gameArray[_jqid].updateBetInformation(_jqnum ,data);
                            });



                        } else {
                            var popWindowNew = bomao.Message.getInstance();
                            var data = {
                                title: '撤单失败(3秒后自动关闭)',
                                content: "<i class=\"ico-waring\"></i><p class=\"pop-text\">撤单失败！已开奖或正在开奖中...</p>",
                                isShowMask: true,
                                cancelIsShow: false,
                                confirmIsShow: false
                            };

                            popWindowNew.show(data);
                            setTimeout(function () {
                                popWindowNew.hide();
                            }, 2800);
                            _c.attr('style', '');

                            $('#r-search').trigger('click');
                        }
                    }
                });
            })
            .on('click', '.cancel', function () {
                // $(this).css('background-image','url(/assets/images/lucky28/confirm-cd.gif) no-repeat center');
                // $(this).css('height','20px');
                var _a = $(this).parent().parent('ul');

                _a.css({
                    'border-bottom': '1px solid #96c0bd',
                    'border-top': '1px solid #96c0bd'
                });


                $(this).removeClass('cancel').addClass('confirm');

            })
            .on('click', '.introduce', function () {
                rZZ(true);
                $('html,body').scrollTop(0).css('overflow', 'hidden');
                var html =
                    '<div class="r-introduce" style="display: block;"><div class="close-order r-close"></div><div class="title">幸运28</div>' +
                    '<div class="row-text">'+
                    '    <p><strong><span style="line-height: 1em;">幸运28-北京</span></strong><span style="line-height: 1em;">：开奖结果来源于北京福彩网北京快乐8(官网)开奖号码，从早上9:05至23:55，每5分钟一期不停开奖。 北京快乐8每期开奖共开出20个数字，幸运28将这20个开奖号码按照由小到大的顺序依次排列；取其1-6位开奖号码相加，和值的末位数作为幸运 28开奖第一个数值； 取其7-12位开奖号码相加，和值的末位数作为幸运28开奖第二个数值，取其13-18位开奖号码相加，和值的末位数作为幸运 28开奖第三个数值；三个数值相加即为幸运28最终的开奖结果。官方网址http://www.bwlc.net</span><br>'+
                    '</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p><strong>幸运28-重庆</strong>：开奖结果来源于重庆市彩票网重庆时时彩(官网)开奖号码，从10:00至22:00，每10分钟一期不停开奖，从22:00至01:55，每5分钟一期不停开奖。'+
                    '        重庆时时彩每期开奖共开出5个数字，幸运28将前三码做为开奖号码。三个数值相加即为幸运28最终的开奖结果。官方网址http://www.cqcp.net</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p><strong>幸运28-黑龙江</strong>：开奖结果来源于黑龙江福彩网黑龙江时时彩(官网)开奖号码，从08:50至22:40，每10分钟一期不停开奖。'+
                    '        黑龙江时时彩每期开奖共开出5个数字，幸运28将前三码做为开奖号码。三个数值相加即为幸运28最终的开奖结果。官方网址http://www.lottost.cn</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p><strong>幸运28-新疆</strong>：开奖结果来源于新疆福彩网新疆时时彩(官网)开奖号码，从10:10至02:00，每10分钟一期不停开奖。'+
                    '        新疆时时彩每期开奖共开出5个数字，幸运28将前三码做为开奖号码。三个数值相加即为幸运28最终的开奖结果。官方网址http://www.xjflcp.com</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p><strong>幸运28-天津</strong>：开奖结果来源于天津福彩网天津时时彩(官网)开奖号码，从09:10至23:00，每10分钟一期不停开奖。'+
                    '        天津时时彩每期开奖共开出5个数字，幸运28将前三码做为开奖号码。三个数值相加即为幸运28最终的开奖结果。官方网址http://www.tjflcpw.com</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p><strong>幸运28-上海</strong>：开奖结果来源于上海福彩网上海时时乐(官网)开奖号码，从10:30至21:30，每30分钟一期不停开奖。上海时时乐每期开奖共开出3个数字，幸运28将这三个号做为开奖号码。三个数值相加即为幸运28最终的开奖结果。官方网址http://www.swlc.gov.cn'+
                    '    </p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p><strong>五大玩法简介</strong></p>'+
                    '    <p>大小玩法：数字14-27为大 数字0-13为小 。</p>'+
                    '    <p>单双玩法: 数字1，3，5，~27为单 数字0，2，4~26为双 。</p>'+
                    '    <p>极值玩法: [极小0-5] [极大22-27] 。</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p>组合玩法:</p>'+
                    '    <p>数字14，16，~26为大双 数字0，2，4，~12为小双。</p>'+
                    '    <p>数字15，17，~27为大单 数字1，3，5，~13为小单。</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p>和值玩法:</p>'+
                    '    <p>从0~27中任何选择1个或1个以上号码，所选数值等于开奖号码的相加之和，即为中奖。</p>'+
                    '</div>'+
                    '</div>';
                $('.record-panel').append(html);
                $('.r-introduce').fadeIn();

            })
            .on('click', '.r-history .a-2 label', function () {
                if($(this).find('input').is(':checked')){
                    $(this).removeClass('check').addClass('checkin')
                }else {
                    $(this).removeClass('checkin').addClass('check')
                }
                var _a = $('.r-history .a-2 li label input:checked').length;
                if (_a == 4) {
                    $('.r-history .a-2 li:eq(3) label input').prop('checked', false);
                    $('.r-history .a-2 li:eq(3) label').removeClass('checkin').addClass('check');
                }

            });

        $('.r-history .a3-2').scroll(function () {
            // $('html').css('overflow-y','hidden');
            var _a = $(this).scrollTop(),
                _d = $(this).find('.l').height() + 1;
            if (_lsadd == '') {
                _lsadd = _address;
            }

            if (_a >= _d - _c) {
                _b += 1;


                $.ajax({
                    url: _lsadd + '&page=' + _b,
                    cache: false,
                    success: function (html) {
                        $('.r-history .a3-2 .l').append(html);

                    }
                });
            }
        });
        $('.r-kjjg .bottom').scroll(function () {

            var _a = $(this).scrollTop(),
                _d = $(this).find('.bottom-a').height() - 1,
                me=this;

            if (_lskjjg == '') {
                _lskjjg = _kjjg;
            }

            if (_a >= _d - _kjjgdiv) {
                _b += 1;

                $.ajax({
                    url: _lskjjg + '&page=' + _b,
                    cache: false,
                    success: function (html) {
                        $('.r-kjjg .main-bot .bot-l .bottom .bottom-a').append(html);

                    }
                });
            }

        });
    })();

    
    //游戏名称 ，游戏ID ， 游戏父容器 ， 游戏赔率设定 ， 游戏新奖期数据 ， 游戏初始化奖期数据
    function creatGame(gameName, gameId, gameUIContainer, gameMethod, newPrizeData, initData) {
        var game = null;
        var time = null;

        var getData = function () {
            if (!game) {

                var clockUIContainer = gameUIContainer + '-clock';
                var clock = new bomao.Lucky28.clock({UIContainer: clockUIContainer, cityName: gameName});
                clock.updateCityName();

                game = new bomao.Lucky28.Game({
                    name: gameName,
                    id: gameId,
                    UIContainer: gameUIContainer,
                    gameMothed: gameMethod,
                    clock: clock
                });
                game._token = newPrizeData._token;
                game.prize_group = newPrizeData.user_prize_group;
                game.bet_max_amount = Number(newPrizeData.bet_max_amount);

                gameArray.push(game);

                //获取游戏订单数组
                game.game_orders = newPrizeData.gameInfo[gameId].orders;

                //模拟三期 需要设置一个时间分界点
                var currentGameData = newPrizeData.gameInfo[gameId];
                //初始化封盘状态判断
                if(currentGameData.currentNumberTime - newPrizeData.currentTime <= currentGameData.cycle){
                    if(initData.length == 3){
                        game.addPrize(initData[2].issue, 0 , currentGameData.cycle , initData[2].wn_number.replace(/\s+/g,""),0);
                        game.addPrize(initData[1].issue, 0 , currentGameData.cycle , initData[1].wn_number.replace(/\s+/g,""),0);
                    }
                    if(initData.length == 2){
                        game.addPrize(initData[1].issue, 0 , currentGameData.cycle , initData[1].wn_number.replace(/\s+/g,""),0);
                    }
                    game.addPrize(currentGameData.currentNumber , (currentGameData.currentNumberTime-newPrizeData.currentTime) , currentGameData.cycle , '' , currentGameData.entertainedTime);
                    time = currentGameData.currentNumberTime - newPrizeData.currentTime;
                }else{
                    //有奖期正常显示，无新奖期则显示历史3期
                    if(currentGameData.currentNumberTime){
                        if(initData.length == 3){
                            game.addPrize(initData[1].issue, 0 , currentGameData.cycle , initData[1].wn_number.replace(/\s+/g,""),0);
                            game.addPrize(initData[0].issue, 0 , currentGameData.cycle , initData[0].wn_number.replace(/\s+/g,""),0);
                        }
                        if(initData.length == 2){
                            game.addPrize(initData[1].issue, 0 , currentGameData.cycle , initData[1].wn_number.replace(/\s+/g,""),0);
                        }
                        game.addPrize(currentGameData.currentNumber , (currentGameData.currentNumberTime-newPrizeData.currentTime) , currentGameData.cycle , '' , currentGameData.entertainedTime);
                        time = currentGameData.currentNumberTime - newPrizeData.currentTime - currentGameData.cycle;
                        //设置开盘时间
                        var startDate = new Date(new Date(currentGameData.gameNumbers[0].time).getTime()-currentGameData.cycle*1000);
                        //IE设置
                        if(isNaN(startDate)){
                            var currentDate = new Date(Date.parse(currentGameData.gameNumbers[0].time.replace(/-/g,"/"))).getTime() - currentGameData.cycle*1000;
                            startDate = new Date(currentDate);
                        }
                        game.caches[0].information_suspension.updateOpenTime(
                            startDate.getMonth()+1,
                            startDate.getDate(),
                            startDate.getHours(),
                            startDate.getMinutes(),
                            startDate.getSeconds()
                            );
                    }else{
                        game.addPrize(initData[2].issue, 0 , currentGameData.cycle , initData[2].wn_number.replace(/\s+/g,""),0);
                        game.addPrize(initData[1].issue, 0 , currentGameData.cycle , initData[1].wn_number.replace(/\s+/g,""),0);
                        game.addPrize(initData[0].issue, 0 , currentGameData.cycle , initData[0].wn_number.replace(/\s+/g,""),0);
                    }
                }

                setTimeout(getData,(time+1)*1000);
                game.initload = false;

                //初始化显示最新奖期
                game.getCurrentPrize().container.show();

                //初始化历史数据
                game.mini_history.getInitData(newPrizeData.gameInfo[gameId].winNumbers);

                //切换奖期
                game.container.find('.bet-history-nav').on('click', 'li', function () {
                    //判断是否处于动画中
                    if(!game.isAnimating){
                        var index = $(this).attr('data-param');
                        // 当前奖期进入动画
                        if(game.currentPrize.prize_id != game.getPrizePeriodByNumber(index).prize_id){
                            if(game.currentPrize.prize_id > game.getPrizePeriodByNumber(index).prize_id){
                                game.container.find('.prize-id-'+game.currentPrize.prize_id).addClass('prize-up-move-miss');
                                game.container.find('.prize-id-'+game.getPrizePeriodByNumber(index).prize_id).addClass('prize-up-move');
                            }else{
                                game.container.find('.prize-id-'+game.currentPrize.prize_id).addClass('prize-down-move-miss');
                                game.container.find('.prize-id-'+game.getPrizePeriodByNumber(index).prize_id).addClass('prize-down-move');
                            }
                        }
                        game.switchPrize(index);
                    };
                });

                //手动切换奖期菜单
                var rec_menu = game.container.find('.bet-history-nav li');
                game.addEvent('afert_select_recompense', function (e, data) {
                    var rec_content = game.container.find('.bet-history-content').children();
                    rec_menu.removeClass('recompense-selected');
                    for (var i = 0; i < data.length; i++) {
                        if (data[i] == 0) {
                            rec_menu.eq(i).addClass('recompense-selected');
                        }
                    }

                    game.container.find('.prize-id-'+game.getCurrentPrize().prize_id).show();
                    if (game.getCurrentPrize()) {
                        game.getCurrentPrize().showplay(game.getCurrentPrize().currentPlayIndex);
                    }
                });

                //自动切换奖期
                game.addEvent('auto_switch_recompense', function (e, data) {
                    var rec_content = game.container.find('.bet-history-content').children();
                    rec_menu.removeClass('recompense-selected');
                    for (var i = 0; i < data.length; i++) {
                        rec_content.eq((2 - i)).hide();
                        if (data[i] == 0) {
                            rec_menu.eq(i).addClass('recompense-selected');
                        }
                    }

                    game.container.find('.prize-id-'+game.getCurrentPrize().prize_id).show();
                    if (game.getCurrentPrize()) {
                        game.getCurrentPrize().showplay(game.getCurrentPrize().currentPlayIndex);
                    }
                });

                //切换游戏玩法
                game.getCurrentPrizeDOM().find('.play-choose').on('click', 'li', function () {
                    var index = Number($(this).attr('data-param'));
                    if(game.getCurrentPrize().currentPlayIndex != index && !game.isAnimating){
                        game.getCurrentPrize().swtichplay(index);
                    }
                });

                //游戏玩法
                game.container.on('click', '.bet li', function () {
                    //当前为组合玩法
                    if (game.getCurrentPrize().currentPlayIndex == 0) {
                        //判断和值是否已有点击效果，如果有，进行清除
                        if (game.getCurrentPrize().play_hezhi.isActivity()) {
                            game.getCurrentPrize().play_hezhi.reSet();
                        }
                        //当前为和值玩法
                    } else {
                        //判断组合是否已有点击效果，如果有，进行清除
                        if (game.getCurrentPrize().play_zuhe.isActivity()) {
                            game.getCurrentPrize().play_zuhe.reSet();
                        }
                    }

                    if ($(this).attr('param')) {
                        game.getCurrentPrize().getCurrentPlay().completeSelect($(this).attr('param'));

                        if (game.getCurrentPrize().getCurrentPlay().squareData[$(this).attr('param')] == -1) {
                            //隐藏下单菜单
                            order_window.closeOrder();
                        } else {
                            //显示下单菜单
                            order_window.showOrderWindow(game, game.getCurrentPrize().getCurrentPlay().current_cell_data);
                        }
                    }
                });

                //和值玩法-鼠标移入显示赔率
                game.container.on('mouseenter', '.bet-panel-hezhi li a', function () {
                    if ($(this).next().hasClass('hezhi-odds-tip-show')) {
                        $(this).next().removeClass('hezhi-odds-tip-show');
                    }
                    $(this).next().addClass('hezhi-odds-tip-show');
                });
                //和值玩法-鼠标移出隐藏赔率
                game.container.on('mouseleave', '.bet-panel-hezhi li a', function () {
                    if ($(this).next().hasClass('hezhi-odds-tip-show')) {
                        $(this).next().removeClass('hezhi-odds-tip-show');
                    }
                });
                //和值机选
                game.container.on('mousedown', '.random-submit', function () {
                    //判断组合是否已有点击效果，如果有，进行清除
                    if (game.getCurrentPrize().play_zuhe.isActivity()) {
                        game.getCurrentPrize().play_zuhe.reSet();
                    }
                    //获取随机数
                    var ran = function () {
                        var val = parseInt(Math.random() * 28);
                        if (val == game.getCurrentPrize().play_hezhi.last_random) {
                            ran();
                        } else {
                            game.getCurrentPrize().play_hezhi.last_random = val;
                        }
                    };

                    ran();
                    game.getCurrentPrize().getCurrentPlay().completeSelect(game.getCurrentPrize().play_hezhi.last_random);
                    $(this).addClass('random-box-active');
                    //显示下单菜单
                    order_window.showOrderWindow(game, game.getCurrentPrize().getCurrentPlay().current_cell_data);
                });
                game.container.on('mouseup', '.random-submit', function () {
                    $(this).removeClass('random-box-active');
                });
                game.container.on('mouseleave', '.random-submit', function () {
                    $(this).removeClass('random-box-active');
                });
                //和值赔率列表
                game.container.on('mouseenter','.odds-explain',function(){
                    game.container.find('.odds-explain-list').removeClass('odds-list-normal').addClass('odds-list-active');
                });
                game.container.on('mouseleave','.odds-explain',function(){
                    game.container.find('.odds-explain-list').removeClass('odds-list-active').addClass('odds-list-normal');
                });

                //取消机选
                // game.container.on('mousedown', '.random-cancel', function () {
                //     game.getCurrentPrize().play_hezhi.reSet();
                //     $(this).addClass('random-box-active');
                //     //隐藏下单菜单
                //     order_window.closeOrder();
                // });
                // game.container.on('mouseup', '.random-cancel', function () {
                //     $(this).removeClass('random-box-active');
                // });
                // game.container.on('mouseleave', '.random-cancel', function () {
                //     $(this).removeClass('random-box-active');
                // });


                var r_dxds = game.mini_history._dxds,
                    r_lushu = game.mini_history._lushu,
                    r_move = game.mini_history._move,
                    r_move1 = game.mini_history._move1,
                    _find = game.mini_history.container;

                _find.on('click', '.ul-1 .dx', function () {
                    _find.find('.ul2-main').css('right', '');
                    r_dxds = 0;
                    game.mini_history._dxds = 0;
                    game.mini_history.getHistory(r_dxds, _find.find('.ul2-main'), game.mini_history.historyArr, r_lushu);

                });

                _find.on('click', '.ul-1 .ds', function () {
                    _find.find('.ul2-main').css('right', '');
                    r_dxds = 1;
                    game.mini_history._dxds = 1;
                    game.mini_history.getHistory(r_dxds, _find.find('.ul2-main'), game.mini_history.historyArr, r_lushu);
                });

                // ico1
                _find.on('click', '.ul-3 .ico1', function () {
                    _find.find('.ul2-main').css('right', '');
                    if (r_lushu == 0) {
                        r_lushu = 1;
                        game.mini_history._lushu = 1;
                    } else {
                        r_lushu = 0;
                        game.mini_history._lushu = 0;
                    }
                    $(this).toggleClass('ico1-1');
                    game.mini_history.getHistory(r_dxds, _find.find('.ul2-main'), game.mini_history.historyArr, r_lushu);
                });

                //走势图拖动
                _find.on('mousedown', '.ul2-main', function (e) {
                    _find.find('.ul-2').css('overflow-x','auto');
                    var _x = e.pageX,
                        _width = parseInt($(this).css('width')),
                        _pyl = _find.find('.ul-2').scrollLeft();
                        // e=e||window.event;

                    $(this).mousemove(function (f) {
                        // var f=e||window.event;
                        r_move1 = -(f.pageX - _x) + _pyl;

                        if (r_move1 <= 0) {
                            r_move1 = 0;
                            $(this).unbind('mousemove');
                            _find.find('.ul2-l').show().fadeOut(1000);
                        } else if (r_move1 > _width - 425) {
                            r_move1 = _width - 425;
                            $(this).unbind('mousemove');
                            _find.find('.ul2-r').show().fadeOut(1000);
                        }

                        _find.find('.ul-2').scrollLeft(r_move1);


                    });
                }).on('mouseup mouseleave', '.ul2-main', function () {

                    _find.find('.ul-2').css('overflow-x','auto');
                    $(this).off('mousemove');
                });

                //走势图按钮，跳转官网
                _find.on('click','.ico2',function(event){
                    game.mini_history.linkOfficePage();
                });

                //走势图按钮，刷新走势图
                _find.on('click', '.ico3', function(event){
                    game.mini_history.updataModel();
                });

            } else {
                service.getGameDataByNumber(gameId, function (data) {
                    if(data.currentNumberTime){
                        
                        if(data.currentNumberTime - data.currentTime <= data.cycle){
                            time = data.currentNumberTime - data.currentTime;
                        }else{
                            time = data.currentNumberTime - data.currentTime - data.cycle;
                        }

                        setTimeout(getData,(time+1)*1000);
                        //相同期期号奖期，只更新，不新创建。用于封盘开始后的第一期
                        if(data.currentNumber == game.priedIDArr[0]){
                            if(data.currentNumberTime - data.currentTime <= data.cycle){
                                game.caches[0].fleshPrize(time,0);
                            }else{
                                game.caches[0].fleshPrize(time,5);
                            }
                        }else{
                            game.addPrize(data.currentNumber , time , data.cycle ,'' , data.entertainedTime);
                            game.autoSwitchPrize(1);
                        }

                        //应该处于停盘阶段
                        if(time > data.cycle){
                            var startDate = new Date(new Date(data.gameNumbers[0].time).getTime()-data.cycle*1000);
                            //IE设置
                            if(isNaN(startDate)){
                                var currentDate = new Date(Date.parse(data.gameNumbers[0].time.replace(/-/g,"/"))).getTime() - data.cycle*1000;
                                startDate = new Date(currentDate);
                            }
                            //设置开盘时间
                            game.caches[0].information_suspension.updateOpenTime(
                                startDate.getMonth()+1,
                                startDate.getDate(),
                                startDate.getHours(),
                                startDate.getMinutes(),
                                startDate.getSeconds()
                                );
                        }
                    }
                });
            }

        }

        getData();
    }
})(bomao);


