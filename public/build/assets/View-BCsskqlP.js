import{A as te}from"./AppLayout-du08q5u_.js";import{s as re}from"./vue-multiselect.css_vue_type_style_index_0_src_true_lang-CRPPjZlv.js";import{B as H,P as D,d as I,R as X,y as ne,x as oe,j as N,o as b,c as A,w,a as k,b as E,e as x,t as W,f as R,u as S,g as ie,z as ae,A as se}from"./app-BcgWRFmc.js";import"./index-DajnbktX.js";import ue from"./PrintCSF-BuehlRBD.js";import{_ as le}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./dar-logo-BEVYNDow.js";/*!
 * qrcode.vue v3.4.1
 * A Vue.js component to generate QRCode.
 * © 2017-2023 @scopewu(https://github.com/scopewu)
 * MIT License.
 */var $=function(){return $=Object.assign||function(d){for(var l,u=1,f=arguments.length;u<f;u++){l=arguments[u];for(var v in l)Object.prototype.hasOwnProperty.call(l,v)&&(d[v]=l[v])}return d},$.apply(this,arguments)};var z;(function(a){var d=function(){function n(e,t,r,o){if(this.version=e,this.errorCorrectionLevel=t,this.modules=[],this.isFunction=[],e<n.MIN_VERSION||e>n.MAX_VERSION)throw new RangeError("Version value out of range");if(o<-1||o>7)throw new RangeError("Mask value out of range");this.size=e*4+17;for(var i=[],s=0;s<this.size;s++)i.push(!1);for(var s=0;s<this.size;s++)this.modules.push(i.slice()),this.isFunction.push(i.slice());this.drawFunctionPatterns();var c=this.addEccAndInterleave(r);if(this.drawCodewords(c),o==-1)for(var m=1e9,s=0;s<8;s++){this.applyMask(s),this.drawFormatBits(s);var p=this.getPenaltyScore();p<m&&(o=s,m=p),this.applyMask(s)}f(0<=o&&o<=7),this.mask=o,this.applyMask(o),this.drawFormatBits(o),this.isFunction=[]}return n.encodeText=function(e,t){var r=a.QrSegment.makeSegments(e);return n.encodeSegments(r,t)},n.encodeBinary=function(e,t){var r=a.QrSegment.makeBytes(e);return n.encodeSegments([r],t)},n.encodeSegments=function(e,t,r,o,i,s){if(r===void 0&&(r=1),o===void 0&&(o=40),i===void 0&&(i=-1),s===void 0&&(s=!0),!(n.MIN_VERSION<=r&&r<=o&&o<=n.MAX_VERSION)||i<-1||i>7)throw new RangeError("Invalid value");var c,m;for(c=r;;c++){var p=n.getNumDataCodewords(c,t)*8,_=v.getTotalBits(e,c);if(_<=p){m=_;break}if(c>=o)throw new RangeError("Data too long")}for(var g=0,C=[n.Ecc.MEDIUM,n.Ecc.QUARTILE,n.Ecc.HIGH];g<C.length;g++){var M=C[g];s&&m<=n.getNumDataCodewords(c,M)*8&&(t=M)}for(var h=[],y=0,L=e;y<L.length;y++){var P=L[y];l(P.mode.modeBits,4,h),l(P.numChars,P.mode.numCharCountBits(c),h);for(var T=0,U=P.getData();T<U.length;T++){var j=U[T];h.push(j)}}f(h.length==m);var Q=n.getNumDataCodewords(c,t)*8;f(h.length<=Q),l(0,Math.min(4,Q-h.length),h),l(0,(8-h.length%8)%8,h),f(h.length%8==0);for(var G=236;h.length<Q;G^=253)l(G,8,h);for(var F=[];F.length*8<h.length;)F.push(0);return h.forEach(function(ee,K){return F[K>>>3]|=ee<<7-(K&7)}),new n(c,t,F,i)},n.prototype.getModule=function(e,t){return 0<=e&&e<this.size&&0<=t&&t<this.size&&this.modules[t][e]},n.prototype.getModules=function(){return this.modules},n.prototype.drawFunctionPatterns=function(){for(var e=0;e<this.size;e++)this.setFunctionModule(6,e,e%2==0),this.setFunctionModule(e,6,e%2==0);this.drawFinderPattern(3,3),this.drawFinderPattern(this.size-4,3),this.drawFinderPattern(3,this.size-4);for(var t=this.getAlignmentPatternPositions(),r=t.length,e=0;e<r;e++)for(var o=0;o<r;o++)e==0&&o==0||e==0&&o==r-1||e==r-1&&o==0||this.drawAlignmentPattern(t[e],t[o]);this.drawFormatBits(0),this.drawVersion()},n.prototype.drawFormatBits=function(e){for(var t=this.errorCorrectionLevel.formatBits<<3|e,r=t,o=0;o<10;o++)r=r<<1^(r>>>9)*1335;var i=(t<<10|r)^21522;f(i>>>15==0);for(var o=0;o<=5;o++)this.setFunctionModule(8,o,u(i,o));this.setFunctionModule(8,7,u(i,6)),this.setFunctionModule(8,8,u(i,7)),this.setFunctionModule(7,8,u(i,8));for(var o=9;o<15;o++)this.setFunctionModule(14-o,8,u(i,o));for(var o=0;o<8;o++)this.setFunctionModule(this.size-1-o,8,u(i,o));for(var o=8;o<15;o++)this.setFunctionModule(8,this.size-15+o,u(i,o));this.setFunctionModule(8,this.size-8,!0)},n.prototype.drawVersion=function(){if(!(this.version<7)){for(var e=this.version,t=0;t<12;t++)e=e<<1^(e>>>11)*7973;var r=this.version<<12|e;f(r>>>18==0);for(var t=0;t<18;t++){var o=u(r,t),i=this.size-11+t%3,s=Math.floor(t/3);this.setFunctionModule(i,s,o),this.setFunctionModule(s,i,o)}}},n.prototype.drawFinderPattern=function(e,t){for(var r=-4;r<=4;r++)for(var o=-4;o<=4;o++){var i=Math.max(Math.abs(o),Math.abs(r)),s=e+o,c=t+r;0<=s&&s<this.size&&0<=c&&c<this.size&&this.setFunctionModule(s,c,i!=2&&i!=4)}},n.prototype.drawAlignmentPattern=function(e,t){for(var r=-2;r<=2;r++)for(var o=-2;o<=2;o++)this.setFunctionModule(e+o,t+r,Math.max(Math.abs(o),Math.abs(r))!=1)},n.prototype.setFunctionModule=function(e,t,r){this.modules[t][e]=r,this.isFunction[t][e]=!0},n.prototype.addEccAndInterleave=function(e){var t=this.version,r=this.errorCorrectionLevel;if(e.length!=n.getNumDataCodewords(t,r))throw new RangeError("Invalid argument");for(var o=n.NUM_ERROR_CORRECTION_BLOCKS[r.ordinal][t],i=n.ECC_CODEWORDS_PER_BLOCK[r.ordinal][t],s=Math.floor(n.getNumRawDataModules(t)/8),c=o-s%o,m=Math.floor(s/o),p=[],_=n.reedSolomonComputeDivisor(i),g=0,C=0;g<o;g++){var M=e.slice(C,C+m-i+(g<c?0:1));C+=M.length;var h=n.reedSolomonComputeRemainder(M,_);g<c&&M.push(0),p.push(M.concat(h))}for(var y=[],L=function(P){p.forEach(function(T,U){(P!=m-i||U>=c)&&y.push(T[P])})},g=0;g<p[0].length;g++)L(g);return f(y.length==s),y},n.prototype.drawCodewords=function(e){if(e.length!=Math.floor(n.getNumRawDataModules(this.version)/8))throw new RangeError("Invalid argument");for(var t=0,r=this.size-1;r>=1;r-=2){r==6&&(r=5);for(var o=0;o<this.size;o++)for(var i=0;i<2;i++){var s=r-i,c=(r+1&2)==0,m=c?this.size-1-o:o;!this.isFunction[m][s]&&t<e.length*8&&(this.modules[m][s]=u(e[t>>>3],7-(t&7)),t++)}}f(t==e.length*8)},n.prototype.applyMask=function(e){if(e<0||e>7)throw new RangeError("Mask value out of range");for(var t=0;t<this.size;t++)for(var r=0;r<this.size;r++){var o=void 0;switch(e){case 0:o=(r+t)%2==0;break;case 1:o=t%2==0;break;case 2:o=r%3==0;break;case 3:o=(r+t)%3==0;break;case 4:o=(Math.floor(r/3)+Math.floor(t/2))%2==0;break;case 5:o=r*t%2+r*t%3==0;break;case 6:o=(r*t%2+r*t%3)%2==0;break;case 7:o=((r+t)%2+r*t%3)%2==0;break;default:throw new Error("Unreachable")}!this.isFunction[t][r]&&o&&(this.modules[t][r]=!this.modules[t][r])}},n.prototype.getPenaltyScore=function(){for(var e=0,t=0;t<this.size;t++){for(var r=!1,o=0,i=[0,0,0,0,0,0,0],s=0;s<this.size;s++)this.modules[t][s]==r?(o++,o==5?e+=n.PENALTY_N1:o>5&&e++):(this.finderPenaltyAddHistory(o,i),r||(e+=this.finderPenaltyCountPatterns(i)*n.PENALTY_N3),r=this.modules[t][s],o=1);e+=this.finderPenaltyTerminateAndCount(r,o,i)*n.PENALTY_N3}for(var s=0;s<this.size;s++){for(var r=!1,c=0,i=[0,0,0,0,0,0,0],t=0;t<this.size;t++)this.modules[t][s]==r?(c++,c==5?e+=n.PENALTY_N1:c>5&&e++):(this.finderPenaltyAddHistory(c,i),r||(e+=this.finderPenaltyCountPatterns(i)*n.PENALTY_N3),r=this.modules[t][s],c=1);e+=this.finderPenaltyTerminateAndCount(r,c,i)*n.PENALTY_N3}for(var t=0;t<this.size-1;t++)for(var s=0;s<this.size-1;s++){var m=this.modules[t][s];m==this.modules[t][s+1]&&m==this.modules[t+1][s]&&m==this.modules[t+1][s+1]&&(e+=n.PENALTY_N2)}for(var p=0,_=0,g=this.modules;_<g.length;_++){var C=g[_];p=C.reduce(function(y,L){return y+(L?1:0)},p)}var M=this.size*this.size,h=Math.ceil(Math.abs(p*20-M*10)/M)-1;return f(0<=h&&h<=9),e+=h*n.PENALTY_N4,f(0<=e&&e<=2568888),e},n.prototype.getAlignmentPatternPositions=function(){if(this.version==1)return[];for(var e=Math.floor(this.version/7)+2,t=this.version==32?26:Math.ceil((this.version*4+4)/(e*2-2))*2,r=[6],o=this.size-7;r.length<e;o-=t)r.splice(1,0,o);return r},n.getNumRawDataModules=function(e){if(e<n.MIN_VERSION||e>n.MAX_VERSION)throw new RangeError("Version number out of range");var t=(16*e+128)*e+64;if(e>=2){var r=Math.floor(e/7)+2;t-=(25*r-10)*r-55,e>=7&&(t-=36)}return f(208<=t&&t<=29648),t},n.getNumDataCodewords=function(e,t){return Math.floor(n.getNumRawDataModules(e)/8)-n.ECC_CODEWORDS_PER_BLOCK[t.ordinal][e]*n.NUM_ERROR_CORRECTION_BLOCKS[t.ordinal][e]},n.reedSolomonComputeDivisor=function(e){if(e<1||e>255)throw new RangeError("Degree out of range");for(var t=[],r=0;r<e-1;r++)t.push(0);t.push(1);for(var o=1,r=0;r<e;r++){for(var i=0;i<t.length;i++)t[i]=n.reedSolomonMultiply(t[i],o),i+1<t.length&&(t[i]^=t[i+1]);o=n.reedSolomonMultiply(o,2)}return t},n.reedSolomonComputeRemainder=function(e,t){for(var r=t.map(function(m){return 0}),o=function(m){var p=m^r.shift();r.push(0),t.forEach(function(_,g){return r[g]^=n.reedSolomonMultiply(_,p)})},i=0,s=e;i<s.length;i++){var c=s[i];o(c)}return r},n.reedSolomonMultiply=function(e,t){if(e>>>8||t>>>8)throw new RangeError("Byte out of range");for(var r=0,o=7;o>=0;o--)r=r<<1^(r>>>7)*285,r^=(t>>>o&1)*e;return f(r>>>8==0),r},n.prototype.finderPenaltyCountPatterns=function(e){var t=e[1];f(t<=this.size*3);var r=t>0&&e[2]==t&&e[3]==t*3&&e[4]==t&&e[5]==t;return(r&&e[0]>=t*4&&e[6]>=t?1:0)+(r&&e[6]>=t*4&&e[0]>=t?1:0)},n.prototype.finderPenaltyTerminateAndCount=function(e,t,r){return e&&(this.finderPenaltyAddHistory(t,r),t=0),t+=this.size,this.finderPenaltyAddHistory(t,r),this.finderPenaltyCountPatterns(r)},n.prototype.finderPenaltyAddHistory=function(e,t){t[0]==0&&(e+=this.size),t.pop(),t.unshift(e)},n.MIN_VERSION=1,n.MAX_VERSION=40,n.PENALTY_N1=3,n.PENALTY_N2=3,n.PENALTY_N3=40,n.PENALTY_N4=10,n.ECC_CODEWORDS_PER_BLOCK=[[-1,7,10,15,20,26,18,20,24,30,18,20,24,26,30,22,24,28,30,28,28,28,28,30,30,26,28,30,30,30,30,30,30,30,30,30,30,30,30,30,30],[-1,10,16,26,18,24,16,18,22,22,26,30,22,22,24,24,28,28,26,26,26,26,28,28,28,28,28,28,28,28,28,28,28,28,28,28,28,28,28,28,28],[-1,13,22,18,26,18,24,18,22,20,24,28,26,24,20,30,24,28,28,26,30,28,30,30,30,30,28,30,30,30,30,30,30,30,30,30,30,30,30,30,30],[-1,17,28,22,16,22,28,26,26,24,28,24,28,22,24,24,30,28,28,26,28,30,24,30,30,30,30,30,30,30,30,30,30,30,30,30,30,30,30,30,30]],n.NUM_ERROR_CORRECTION_BLOCKS=[[-1,1,1,1,1,1,2,2,2,2,4,4,4,4,4,6,6,6,6,7,8,8,9,9,10,12,12,12,13,14,15,16,17,18,19,19,20,21,22,24,25],[-1,1,1,1,2,2,4,4,4,5,5,5,8,9,9,10,10,11,13,14,16,17,17,18,20,21,23,25,26,28,29,31,33,35,37,38,40,43,45,47,49],[-1,1,1,2,2,4,4,6,6,8,8,8,10,12,16,12,17,16,18,21,20,23,23,25,27,29,34,34,35,38,40,43,45,48,51,53,56,59,62,65,68],[-1,1,1,2,4,4,4,5,6,8,8,11,11,16,16,18,16,19,21,25,25,25,34,30,32,35,37,40,42,45,48,51,54,57,60,63,66,70,74,77,81]],n}();a.QrCode=d;function l(n,e,t){if(e<0||e>31||n>>>e)throw new RangeError("Value out of range");for(var r=e-1;r>=0;r--)t.push(n>>>r&1)}function u(n,e){return(n>>>e&1)!=0}function f(n){if(!n)throw new Error("Assertion error")}var v=function(){function n(e,t,r){if(this.mode=e,this.numChars=t,this.bitData=r,t<0)throw new RangeError("Invalid argument");this.bitData=r.slice()}return n.makeBytes=function(e){for(var t=[],r=0,o=e;r<o.length;r++){var i=o[r];l(i,8,t)}return new n(n.Mode.BYTE,e.length,t)},n.makeNumeric=function(e){if(!n.isNumeric(e))throw new RangeError("String contains non-numeric characters");for(var t=[],r=0;r<e.length;){var o=Math.min(e.length-r,3);l(parseInt(e.substring(r,r+o),10),o*3+1,t),r+=o}return new n(n.Mode.NUMERIC,e.length,t)},n.makeAlphanumeric=function(e){if(!n.isAlphanumeric(e))throw new RangeError("String contains unencodable characters in alphanumeric mode");var t=[],r;for(r=0;r+2<=e.length;r+=2){var o=n.ALPHANUMERIC_CHARSET.indexOf(e.charAt(r))*45;o+=n.ALPHANUMERIC_CHARSET.indexOf(e.charAt(r+1)),l(o,11,t)}return r<e.length&&l(n.ALPHANUMERIC_CHARSET.indexOf(e.charAt(r)),6,t),new n(n.Mode.ALPHANUMERIC,e.length,t)},n.makeSegments=function(e){return e==""?[]:n.isNumeric(e)?[n.makeNumeric(e)]:n.isAlphanumeric(e)?[n.makeAlphanumeric(e)]:[n.makeBytes(n.toUtf8ByteArray(e))]},n.makeEci=function(e){var t=[];if(e<0)throw new RangeError("ECI assignment value out of range");if(e<128)l(e,8,t);else if(e<16384)l(2,2,t),l(e,14,t);else if(e<1e6)l(6,3,t),l(e,21,t);else throw new RangeError("ECI assignment value out of range");return new n(n.Mode.ECI,0,t)},n.isNumeric=function(e){return n.NUMERIC_REGEX.test(e)},n.isAlphanumeric=function(e){return n.ALPHANUMERIC_REGEX.test(e)},n.prototype.getData=function(){return this.bitData.slice()},n.getTotalBits=function(e,t){for(var r=0,o=0,i=e;o<i.length;o++){var s=i[o],c=s.mode.numCharCountBits(t);if(s.numChars>=1<<c)return 1/0;r+=4+c+s.bitData.length}return r},n.toUtf8ByteArray=function(e){e=encodeURI(e);for(var t=[],r=0;r<e.length;r++)e.charAt(r)!="%"?t.push(e.charCodeAt(r)):(t.push(parseInt(e.substring(r+1,r+3),16)),r+=2);return t},n.NUMERIC_REGEX=/^[0-9]*$/,n.ALPHANUMERIC_REGEX=/^[A-Z0-9 $%*+.\/:-]*$/,n.ALPHANUMERIC_CHARSET="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ $%*+-./:",n}();a.QrSegment=v})(z||(z={}));(function(a){(function(d){var l=function(){function u(f,v){this.ordinal=f,this.formatBits=v}return u.LOW=new u(0,1),u.MEDIUM=new u(1,0),u.QUARTILE=new u(2,3),u.HIGH=new u(3,2),u}();d.Ecc=l})(a.QrCode||(a.QrCode={}))})(z||(z={}));(function(a){(function(d){var l=function(){function u(f,v){this.modeBits=f,this.numBitsCharCount=v}return u.prototype.numCharCountBits=function(f){return this.numBitsCharCount[Math.floor((f+7)/17)]},u.NUMERIC=new u(1,[10,12,14]),u.ALPHANUMERIC=new u(2,[9,11,13]),u.BYTE=new u(4,[8,16,16]),u.KANJI=new u(8,[8,10,12]),u.ECI=new u(7,[0,0,0]),u}();d.Mode=l})(a.QrSegment||(a.QrSegment={}))})(z||(z={}));var B=z,J="H",V={L:B.QrCode.Ecc.LOW,M:B.QrCode.Ecc.MEDIUM,Q:B.QrCode.Ecc.QUARTILE,H:B.QrCode.Ecc.HIGH},de=function(){try{new Path2D().addPath(new Path2D)}catch{return!1}return!0}();function Z(a){return a in V}function q(a,d){d===void 0&&(d=0);var l=[];return a.forEach(function(u,f){var v=null;u.forEach(function(n,e){if(!n&&v!==null){l.push("M".concat(v+d," ").concat(f+d,"h").concat(e-v,"v1H").concat(v+d,"z")),v=null;return}if(e===u.length-1){if(!n)return;v===null?l.push("M".concat(e+d,",").concat(f+d," h1v1H").concat(e+d,"z")):l.push("M".concat(v+d,",").concat(f+d," h").concat(e+1-v,"v1H").concat(v+d,"z"));return}n&&v===null&&(v=e)})}),l.join("")}var Y={value:{type:String,required:!0,default:""},size:{type:Number,default:100},level:{type:String,default:J,validator:function(a){return Z(a)}},background:{type:String,default:"#fff"},foreground:{type:String,default:"#000"},margin:{type:Number,required:!1,default:0}},ce=$($({},Y),{renderAs:{type:String,required:!1,default:"canvas",validator:function(a){return["canvas","svg"].indexOf(a)>-1}}}),fe=H({name:"QRCodeSvg",props:Y,setup:function(a){var d=I(0),l=I(""),u=function(){var f=a.value,v=a.level,n=a.margin,e=B.QrCode.encodeText(f,V[v]).getModules();d.value=e.length+n*2,l.value=q(e,n)};return u(),X(u),function(){return D("svg",{width:a.size,height:a.size,"shape-rendering":"crispEdges",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 ".concat(d.value," ").concat(d.value)},[D("path",{fill:a.background,d:"M0,0 h".concat(d.value,"v").concat(d.value,"H0z")}),D("path",{fill:a.foreground,d:l.value})])}}}),ve=H({name:"QRCodeCanvas",props:Y,setup:function(a){var d=I(null),l=function(){var u=a.value,f=a.level,v=a.size,n=a.margin,e=a.background,t=a.foreground,r=d.value;if(r){var o=r.getContext("2d");if(o){var i=B.QrCode.encodeText(u,V[f]).getModules(),s=i.length+n*2,c=window.devicePixelRatio||1,m=v/s*c;r.height=r.width=v*c,o.scale(m,m),o.fillStyle=e,o.fillRect(0,0,s,s),o.fillStyle=t,de?o.fill(new Path2D(q(i,n))):i.forEach(function(p,_){p.forEach(function(g,C){g&&o.fillRect(C+n,_+n,1,1)})})}}};return ne(l),X(l),function(){return D("canvas",{ref:d,style:{width:"".concat(a.size,"px"),height:"".concat(a.size,"px")}})}}}),O=H({name:"Qrcode",render:function(){var a=this.$props,d=a.renderAs,l=a.value,u=a.size,f=a.margin,v=a.level,n=a.background,e=a.foreground,t=u>>>0,r=f>>>0,o=Z(v)?v:J;return D(d==="svg"?fe:ve,{value:l,size:t,margin:r,level:o,background:n,foreground:e})},props:ce});const he=a=>(ae("data-v-497e8866"),a=a(),se(),a),me=he(()=>k("h2",{class:"font-semibold text-xl text-gray-800 leading-tight"}," View ",-1)),ge={class:"py-10",style:{"margin-left":"80px","margin-right":"80px"}},pe={class:"max-w-7x1 mx-auto sm:px-6 lg:px-8"},_e={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg"},Ee={key:0},we={key:1},Ce={class:"p-5 m-5",label:"URL"},Me={key:0},be={style:{display:"flex","justify-content":"center","align-items":"center"},class:"mb-10"},Re={__name:"View",props:{division:Object,section:Object,sub_section_types:Object,user:Object},setup(a){const d=a,l=oe({generated_url:null,selected_sub_section:"",sub_section_type:"",client_type:""}),u=I(null),f=I(!1),v=async(o,i)=>{f.value=!0,u.value=0,d.section&&Array.isArray(d.section.data)&&d.section.data.length>0&&d.section.data[0]?(u.value=0,l.generated_url=n+"/divisions/csf?office_id="+d.user.office_id+"&division_id="+d.division.id+"&section_id="+d.section.data[0].id):(u.value=1,l.generated_url=n+"/divisions/csf?office_id="+d.user.office_id+"&division_id="+d.division.id)},n=window.location.origin,e=I(!1),t=()=>{const o=document.createElement("textarea");o.value=l.generated_url,document.body.appendChild(o),o.select(),document.execCommand("copy"),document.body.removeChild(o),e.value=!0,setTimeout(()=>{e.value=!1},2e3)},r=I(!1);return(o,i)=>{const s=N("v-divider"),c=N("v-card-title"),m=N("v-card"),p=N("v-col"),_=N("v-btn"),g=N("v-row"),C=N("v-text-field"),M=N("v-card-body");return b(),A(te,{title:"Dashboard"},{header:w(()=>[me]),default:w(()=>[k("div",ge,[k("div",pe,[k("div",_e,[E(m,{class:"mb-3"},{default:w(()=>[E(c,{class:"m-3"},{default:w(()=>[a.division?(b(),x("div",Ee," DIVISION : "+W(a.division.division_name),1)):R("",!0),E(s,{class:"border-opacity-100"}),a.section&&Array.isArray(a.section.data)&&a.section.data.length>0&&a.section.data[0]?(b(),x("div",we," SECTION : "+W(a.section.data[0].section_name),1)):R("",!0)]),_:1})]),_:1}),E(m,{class:"mb-3",height:"600px"},{default:w(()=>[E(M,{class:"overflow-visible"},{default:w(()=>[E(g,{class:"p-5",key:""},{default:w(()=>[a.sub_section_types.length>0&&l.selected_sub_section?(b(),A(p,{key:0,class:"my-auto"},{default:w(()=>[E(S(re),{modelValue:l.sub_section_type,"onUpdate:modelValue":i[0]||(i[0]=h=>l.sub_section_type=h),options:a.sub_section_types,multiple:!1,placeholder:"Select Sub Section Type",label:"type_name","track-by":"type_name","allow-empty":!1},null,8,["modelValue","options"])]),_:1})):R("",!0),E(p,{class:"my-auto text-right"},{default:w(()=>[E(_,{"prepend-icon":"mdi-plus",onClick:i[1]||(i[1]=h=>v(l.selected_sub_section,l.sub_section_type))},{default:w(()=>[ie("Generate URL ")]),_:1})]),_:1})]),_:1}),k("div",Ce,[E(g,null,{default:w(()=>[E(p,{cols:"10",md:"11"},{default:w(()=>[E(C,{modelValue:l.generated_url,"onUpdate:modelValue":i[2]||(i[2]=h=>l.generated_url=h),variant:"outlined",label:"URL",readonly:""},null,8,["modelValue"])]),_:1}),E(p,null,{default:w(()=>[E(_,{color:"none",icon:"mdi-content-copy",onClick:i[3]||(i[3]=h=>t())}),e.value?(b(),x("span",Me,"copied")):R("",!0)]),_:1})]),_:1})]),k("div",be,[u.value==0?(b(),A(O,{key:0,"render-as":"svg",value:`${S(n)}/divisions/csf?office_id=${a.user.office_id}&division_id=${d.division.id}&section_id=${d.section.data[0].id}`,size:145,foreground:"#000",level:"L",style:{border:"3px #ffffff solid",width:"300px",height:"300px"}},null,8,["value"])):R("",!0),u.value==1?(b(),A(O,{key:1,"render-as":"svg",value:`${S(n)}/divisions/csf?office_id=${a.user.office_id}&division_id=${d.division.id}`,size:145,foreground:"#000",level:"L",style:{border:"3px #ffffff solid",width:"300px",height:"300px"}},null,8,["value"])):R("",!0),u.value==1.1?(b(),A(O,{key:2,"render-as":"svg",value:`${S(n)}/divisions/csf?office_id=${a.user.office_id}&division_id=${d.division.id}&section_id=${a.section.data[0].id}&sub_section_id=${l.selected_sub_section.id}`,size:145,foreground:"#000",level:"L",style:{border:"3px #ffffff solid",width:"300px",height:"300px"}},null,8,["value"])):R("",!0),u.value==1.2?(b(),A(O,{key:3,"render-as":"svg",value:`${S(n)}/divisions/csf?office_id=${a.user.office_id}&division_id=${d.division.id}&section_id=${a.section.data[0].id}&sub_section_id=${l.selected_sub_section.id}&sub_section_type=${l.sub_section_type.id}`,size:145,foreground:"#000",level:"L",style:{border:"3px #ffffff solid",width:"300px",height:"300px"}},null,8,["value"])):R("",!0),u.value==2?(b(),A(O,{key:4,"render-as":"svg",value:`${S(n)}/divisions/csf?office_id=${a.user.office_id}&division_id=${d.division.id}&section_id=${a.section.data[0].id}&sub_section_id=${l.selected_sub_section.id}`,size:145,foreground:"#000",level:"L",style:{border:"3px #ffffff solid",width:"300px",height:"300px"}},null,8,["value"])):R("",!0),u.value==3?(b(),A(O,{key:5,"render-as":"svg",value:`${S(n)}/divisions/csf?office_id=${a.user.office_id}&division_id=${d.division.id}&section_id=${a.section.data[0].id}`,size:145,foreground:"#000",level:"L",style:{border:"3px #ffffff solid",width:"300px",height:"300px"}},null,8,["value"])):R("",!0)])]),_:1})]),_:1})])])]),f.value==!0?(b(),A(ue,{key:0,is_printing:r.value,form:l,data:d},null,8,["is_printing","form","data"])):R("",!0)]),_:1})}}},Le=le(Re,[["__scopeId","data-v-497e8866"]]);export{Le as default};
