var checkForMathML=true;var notifyIfNoMathML=false;var alertIfNoMathML=false;var mathcolor="";var mathfontfamily="serif";var displaystyle=true;var showasciiformulaonhover=true;var decimalsign=".";var AMdelimiter1="`",AMescape1="\\\\`";var doubleblankmathdelimiter=false;var noMathRender=false;var isIE=document.createElementNS==null;if(document.getElementById==null){alert("This webpage requires a recent browser such as\nMozilla/Netscape 7+ or Internet Explorer 6+MathPlayer")}function AMcreateElementXHTML(a){if(isIE){return document.createElement(a)}else{return document.createElementNS("http://www.w3.org/1999/xhtml",a)}}function AMnoMathMLNote(){var b=AMcreateElementXHTML("h3");b.setAttribute("align","center");b.appendChild(AMcreateElementXHTML("p"));b.appendChild(document.createTextNode("To view the "));var a=AMcreateElementXHTML("a");a.appendChild(document.createTextNode("ASCIIMathML"));a.setAttribute("href","http://www.chapman.edu/~jipsen/asciimath.html");b.appendChild(a);b.appendChild(document.createTextNode(" notation use Internet Explorer 6+"));a=AMcreateElementXHTML("a");a.appendChild(document.createTextNode("MathPlayer"));a.setAttribute("href","http://www.dessci.com/en/products/mathplayer/download.htm");b.appendChild(a);b.appendChild(document.createTextNode(" or Netscape/Mozilla/Firefox"));b.appendChild(AMcreateElementXHTML("p"));return b}function AMisMathMLavailable(){if(navigator.product&&navigator.product=="Gecko"){var c=navigator.userAgent.toLowerCase().match(/rv:\s*([\d\.]+)/)[1].split(".");if(c.length<3){c[2]=0}if(c.length<2){c[1]=0}if(10000*c[0]+100*c[1]+1*c[2]>=10100){return null}else{return AMnoMathMLNote()}}else{if(navigator.appName.slice(0,9)=="Microsoft"){try{var b=new ActiveXObject("MathPlayer.Factory.1");return null}catch(a){return AMnoMathMLNote()}}else{return AMnoMathMLNote()}}}var AMcal=[61237,8492,61238,61239,8496,8497,61240,8459,8464,61241,61242,8466,8499,61243,61244,61245,61246,8475,61247,61248,61249,61250,61251,61252,61253,61254];var AMfrk=[61277,61278,8493,61279,61280,61281,61282,8460,8465,61283,61284,61285,61286,61287,61288,61289,61290,8476,61291,61292,61293,61294,61295,61296,61297,8488];var AMbbb=[61324,61325,8450,61326,61327,61328,61329,8461,61330,61331,61332,61333,61334,8469,61335,8473,8474,8477,61336,61337,61338,61339,61340,61341,61342,8484];var CONST=0,UNARY=1,BINARY=2,INFIX=3,LEFTBRACKET=4,RIGHTBRACKET=5,SPACE=6,UNDEROVER=7,DEFINITION=8,LEFTRIGHT=9,TEXT=10;var AMsqrt={input:"sqrt",tag:"msqrt",output:"sqrt",tex:null,ttype:UNARY},AMroot={input:"root",tag:"mroot",output:"root",tex:null,ttype:BINARY},AMfrac={input:"frac",tag:"mfrac",output:"/",tex:null,ttype:BINARY},AMdiv={input:"/",tag:"mfrac",output:"/",tex:null,ttype:INFIX},AMover={input:"stackrel",tag:"mover",output:"stackrel",tex:null,ttype:BINARY},AMsub={input:"_",tag:"msub",output:"_",tex:null,ttype:INFIX},AMsup={input:"^",tag:"msup",output:"^",tex:null,ttype:INFIX},AMtext={input:"text",tag:"mtext",output:"text",tex:null,ttype:TEXT},AMmbox={input:"mbox",tag:"mtext",output:"mbox",tex:null,ttype:TEXT},AMquote={input:'"',tag:"mtext",output:"mbox",tex:null,ttype:TEXT};var AMsymbols=[{input:"alpha",tag:"mi",output:"\u03B1",tex:null,ttype:CONST},{input:"beta",tag:"mi",output:"\u03B2",tex:null,ttype:CONST},{input:"chi",tag:"mi",output:"\u03C7",tex:null,ttype:CONST},{input:"delta",tag:"mi",output:"\u03B4",tex:null,ttype:CONST},{input:"Delta",tag:"mo",output:"\u0394",tex:null,ttype:CONST},{input:"epsi",tag:"mi",output:"\u03B5",tex:"epsilon",ttype:CONST},{input:"varepsilon",tag:"mi",output:"\u025B",tex:null,ttype:CONST},{input:"eta",tag:"mi",output:"\u03B7",tex:null,ttype:CONST},{input:"gamma",tag:"mi",output:"\u03B3",tex:null,ttype:CONST},{input:"Gamma",tag:"mo",output:"\u0393",tex:null,ttype:CONST},{input:"iota",tag:"mi",output:"\u03B9",tex:null,ttype:CONST},{input:"kappa",tag:"mi",output:"\u03BA",tex:null,ttype:CONST},{input:"lambda",tag:"mi",output:"\u03BB",tex:null,ttype:CONST},{input:"Lambda",tag:"mo",output:"\u039B",tex:null,ttype:CONST},{input:"mu",tag:"mi",output:"\u03BC",tex:null,ttype:CONST},{input:"nu",tag:"mi",output:"\u03BD",tex:null,ttype:CONST},{input:"omega",tag:"mi",output:"\u03C9",tex:null,ttype:CONST},{input:"Omega",tag:"mo",output:"\u03A9",tex:null,ttype:CONST},{input:"phi",tag:"mi",output:"\u03C6",tex:null,ttype:CONST},{input:"varphi",tag:"mi",output:"\u03D5",tex:null,ttype:CONST},{input:"Phi",tag:"mo",output:"\u03A6",tex:null,ttype:CONST},{input:"pi",tag:"mi",output:"\u03C0",tex:null,ttype:CONST},{input:"Pi",tag:"mo",output:"\u03A0",tex:null,ttype:CONST},{input:"psi",tag:"mi",output:"\u03C8",tex:null,ttype:CONST},{input:"Psi",tag:"mi",output:"\u03A8",tex:null,ttype:CONST},{input:"rho",tag:"mi",output:"\u03C1",tex:null,ttype:CONST},{input:"sigma",tag:"mi",output:"\u03C3",tex:null,ttype:CONST},{input:"Sigma",tag:"mo",output:"\u03A3",tex:null,ttype:CONST},{input:"tau",tag:"mi",output:"\u03C4",tex:null,ttype:CONST},{input:"theta",tag:"mi",output:"\u03B8",tex:null,ttype:CONST},{input:"vartheta",tag:"mi",output:"\u03D1",tex:null,ttype:CONST},{input:"Theta",tag:"mo",output:"\u0398",tex:null,ttype:CONST},{input:"upsilon",tag:"mi",output:"\u03C5",tex:null,ttype:CONST},{input:"xi",tag:"mi",output:"\u03BE",tex:null,ttype:CONST},{input:"Xi",tag:"mo",output:"\u039E",tex:null,ttype:CONST},{input:"zeta",tag:"mi",output:"\u03B6",tex:null,ttype:CONST},{input:"*",tag:"mo",output:"\u22C5",tex:"cdot",ttype:CONST},{input:"**",tag:"mo",output:"\u22C6",tex:"star",ttype:CONST},{input:"//",tag:"mo",output:"/",tex:null,ttype:CONST},{input:"\\\\",tag:"mo",output:"\\",tex:"backslash",ttype:CONST},{input:"setminus",tag:"mo",output:"\\",tex:null,ttype:CONST},{input:"xx",tag:"mo",output:"\u00D7",tex:"times",ttype:CONST},{input:"-:",tag:"mo",output:"\u00F7",tex:"div",ttype:CONST},{input:"divide",tag:"mo",output:"-:",tex:null,ttype:DEFINITION},{input:"@",tag:"mo",output:"\u2218",tex:"circ",ttype:CONST},{input:"o+",tag:"mo",output:"\u2295",tex:"oplus",ttype:CONST},{input:"ox",tag:"mo",output:"\u2297",tex:"otimes",ttype:CONST},{input:"o.",tag:"mo",output:"\u2299",tex:"odot",ttype:CONST},{input:"sum",tag:"mo",output:"\u2211",tex:null,ttype:UNDEROVER},{input:"prod",tag:"mo",output:"\u220F",tex:null,ttype:UNDEROVER},{input:"^^",tag:"mo",output:"\u2227",tex:"wedge",ttype:CONST},{input:"^^^",tag:"mo",output:"\u22C0",tex:"bigwedge",ttype:UNDEROVER},{input:"vv",tag:"mo",output:"\u2228",tex:"vee",ttype:CONST},{input:"vvv",tag:"mo",output:"\u22C1",tex:"bigvee",ttype:UNDEROVER},{input:"nn",tag:"mo",output:"\u2229",tex:"cap",ttype:CONST},{input:"nnn",tag:"mo",output:"\u22C2",tex:"bigcap",ttype:UNDEROVER},{input:"uu",tag:"mo",output:"\u222A",tex:"cup",ttype:CONST},{input:"uuu",tag:"mo",output:"\u22C3",tex:"bigcup",ttype:UNDEROVER},{input:"!=",tag:"mo",output:"\u2260",tex:"ne",ttype:CONST},{input:":=",tag:"mo",output:":=",tex:null,ttype:CONST},{input:"lt",tag:"mo",output:"<",tex:null,ttype:CONST},{input:"gt",tag:"mo",output:">",tex:null,ttype:CONST},{input:"<=",tag:"mo",output:"\u2264",tex:"le",ttype:CONST},{input:"lt=",tag:"mo",output:"\u2264",tex:"leq",ttype:CONST},{input:"gt=",tag:"mo",output:"\u2265",tex:"geq",ttype:CONST},{input:">=",tag:"mo",output:"\u2265",tex:"ge",ttype:CONST},{input:"geq",tag:"mo",output:"\u2265",tex:null,ttype:CONST},{input:"-<",tag:"mo",output:"\u227A",tex:"prec",ttype:CONST},{input:"-lt",tag:"mo",output:"\u227A",tex:null,ttype:CONST},{input:">-",tag:"mo",output:"\u227B",tex:"succ",ttype:CONST},{input:"-<=",tag:"mo",output:"\u2AAF",tex:"preceq",ttype:CONST},{input:">-=",tag:"mo",output:"\u2AB0",tex:"succeq",ttype:CONST},{input:"in",tag:"mo",output:"\u2208",tex:null,ttype:CONST},{input:"!in",tag:"mo",output:"\u2209",tex:"notin",ttype:CONST},{input:"sub",tag:"mo",output:"\u2282",tex:"subset",ttype:CONST},{input:"sup",tag:"mo",output:"\u2283",tex:"supset",ttype:CONST},{input:"sube",tag:"mo",output:"\u2286",tex:"subseteq",ttype:CONST},{input:"supe",tag:"mo",output:"\u2287",tex:"supseteq",ttype:CONST},{input:"-=",tag:"mo",output:"\u2261",tex:"equiv",ttype:CONST},{input:"~=",tag:"mo",output:"\u2245",tex:"cong",ttype:CONST},{input:"~~",tag:"mo",output:"\u2248",tex:"approx",ttype:CONST},{input:"prop",tag:"mo",output:"\u221D",tex:"propto",ttype:CONST},{input:"and",tag:"mtext",output:"and",tex:null,ttype:SPACE},{input:"or",tag:"mtext",output:"or",tex:null,ttype:SPACE},{input:"not",tag:"mo",output:"\u00AC",tex:"neg",ttype:CONST},{input:"=>",tag:"mo",output:"\u21D2",tex:"implies",ttype:CONST},{input:"if",tag:"mo",output:"if",tex:null,ttype:SPACE},{input:"<=>",tag:"mo",output:"\u21D4",tex:"iff",ttype:CONST},{input:"AA",tag:"mo",output:"\u2200",tex:"forall",ttype:CONST},{input:"EE",tag:"mo",output:"\u2203",tex:"exists",ttype:CONST},{input:"_|_",tag:"mo",output:"\u22A5",tex:"bot",ttype:CONST},{input:"TT",tag:"mo",output:"\u22A4",tex:"top",ttype:CONST},{input:"|--",tag:"mo",output:"\u22A2",tex:"vdash",ttype:CONST},{input:"|==",tag:"mo",output:"\u22A8",tex:"models",ttype:CONST},{input:"(",tag:"mo",output:"(",tex:null,ttype:LEFTBRACKET},{input:")",tag:"mo",output:")",tex:null,ttype:RIGHTBRACKET},{input:"[",tag:"mo",output:"[",tex:null,ttype:LEFTBRACKET},{input:"]",tag:"mo",output:"]",tex:null,ttype:RIGHTBRACKET},{input:"{",tag:"mo",output:"{",tex:null,ttype:LEFTBRACKET},{input:"}",tag:"mo",output:"}",tex:null,ttype:RIGHTBRACKET},{input:"|",tag:"mo",output:"|",tex:null,ttype:LEFTRIGHT},{input:"(:",tag:"mo",output:"\u2329",tex:"langle",ttype:LEFTBRACKET},{input:":)",tag:"mo",output:"\u232A",tex:"rangle",ttype:RIGHTBRACKET},{input:"<<",tag:"mo",output:"\u2329",tex:null,ttype:LEFTBRACKET},{input:">>",tag:"mo",output:"\u232A",tex:null,ttype:RIGHTBRACKET},{input:"{:",tag:"mo",output:"{:",tex:null,ttype:LEFTBRACKET,invisible:true},{input:":}",tag:"mo",output:":}",tex:null,ttype:RIGHTBRACKET,invisible:true},{input:"int",tag:"mo",output:"\u222B",tex:null,ttype:CONST},{input:"dx",tag:"mi",output:"{:d x:}",tex:null,ttype:DEFINITION},{input:"dy",tag:"mi",output:"{:d y:}",tex:null,ttype:DEFINITION},{input:"dz",tag:"mi",output:"{:d z:}",tex:null,ttype:DEFINITION},{input:"dt",tag:"mi",output:"{:d t:}",tex:null,ttype:DEFINITION},{input:"oint",tag:"mo",output:"\u222E",tex:null,ttype:CONST},{input:"del",tag:"mo",output:"\u2202",tex:"partial",ttype:CONST},{input:"grad",tag:"mo",output:"\u2207",tex:"nabla",ttype:CONST},{input:"+-",tag:"mo",output:"\u00B1",tex:"pm",ttype:CONST},{input:"O/",tag:"mo",output:"\u2205",tex:"emptyset",ttype:CONST},{input:"oo",tag:"mo",output:"\u221E",tex:"infty",ttype:CONST},{input:"aleph",tag:"mo",output:"\u2135",tex:null,ttype:CONST},{input:"...",tag:"mo",output:"...",tex:"ldots",ttype:CONST},{input:":.",tag:"mo",output:"\u2234",tex:"therefore",ttype:CONST},{input:"/_",tag:"mo",output:"\u2220",tex:"angle",ttype:CONST},{input:"\\ ",tag:"mo",output:"\u00A0",tex:null,ttype:CONST},{input:"quad",tag:"mo",output:"\u00A0\u00A0",tex:null,ttype:CONST},{input:"qquad",tag:"mo",output:"\u00A0\u00A0\u00A0\u00A0",tex:null,ttype:CONST},{input:"cdots",tag:"mo",output:"\u22EF",tex:null,ttype:CONST},{input:"vdots",tag:"mo",output:"\u22EE",tex:null,ttype:CONST},{input:"ddots",tag:"mo",output:"\u22F1",tex:null,ttype:CONST},{input:"diamond",tag:"mo",output:"\u22C4",tex:null,ttype:CONST},{input:"square",tag:"mo",output:"\u25A1",tex:null,ttype:CONST},{input:"|__",tag:"mo",output:"\u230A",tex:"lfloor",ttype:CONST},{input:"__|",tag:"mo",output:"\u230B",tex:"rfloor",ttype:CONST},{input:"|~",tag:"mo",output:"\u2308",tex:"lceiling",ttype:CONST},{input:"~|",tag:"mo",output:"\u2309",tex:"rceiling",ttype:CONST},{input:"CC",tag:"mo",output:"\u2102",tex:null,ttype:CONST},{input:"NN",tag:"mo",output:"\u2115",tex:null,ttype:CONST},{input:"QQ",tag:"mo",output:"\u211A",tex:null,ttype:CONST},{input:"RR",tag:"mo",output:"\u211D",tex:null,ttype:CONST},{input:"ZZ",tag:"mo",output:"\u2124",tex:null,ttype:CONST},{input:"f",tag:"mi",output:"f",tex:null,ttype:UNARY,func:true},{input:"g",tag:"mi",output:"g",tex:null,ttype:UNARY,func:true},{input:"'",tag:"mo",output:"\u2032",tex:"prime",ttype:CONST},{input:"lim",tag:"mo",output:"lim",tex:null,ttype:UNDEROVER},{input:"Lim",tag:"mo",output:"Lim",tex:null,ttype:UNDEROVER},{input:"sin",tag:"mo",output:"sin",tex:null,ttype:UNARY,func:true},{input:"cos",tag:"mo",output:"cos",tex:null,ttype:UNARY,func:true},{input:"tan",tag:"mo",output:"tan",tex:null,ttype:UNARY,func:true},{input:"arcsin",tag:"mo",output:"arcsin",tex:null,ttype:UNARY,func:true},{input:"arccos",tag:"mo",output:"arccos",tex:null,ttype:UNARY,func:true},{input:"arctan",tag:"mo",output:"arctan",tex:null,ttype:UNARY,func:true},{input:"sinh",tag:"mo",output:"sinh",tex:null,ttype:UNARY,func:true},{input:"cosh",tag:"mo",output:"cosh",tex:null,ttype:UNARY,func:true},{input:"tanh",tag:"mo",output:"tanh",tex:null,ttype:UNARY,func:true},{input:"coth",tag:"mo",output:"coth",tex:null,ttype:UNARY,func:true},{input:"sech",tag:"mo",output:"sech",tex:null,ttype:UNARY,func:true},{input:"csch",tag:"mo",output:"csch",tex:null,ttype:UNARY,func:true},{input:"cot",tag:"mo",output:"cot",tex:null,ttype:UNARY,func:true},{input:"sec",tag:"mo",output:"sec",tex:null,ttype:UNARY,func:true},{input:"csc",tag:"mo",output:"csc",tex:null,ttype:UNARY,func:true},{input:"log",tag:"mo",output:"log",tex:null,ttype:UNARY,func:true},{input:"ln",tag:"mo",output:"ln",tex:null,ttype:UNARY,func:true},{input:"abs",tag:"mo",output:"abs",tex:null,ttype:UNARY,func:true},{input:"Sin",tag:"mo",output:"sin",tex:null,ttype:UNARY,func:true},{input:"Cos",tag:"mo",output:"cos",tex:null,ttype:UNARY,func:true},{input:"Tan",tag:"mo",output:"tan",tex:null,ttype:UNARY,func:true},{input:"Arcsin",tag:"mo",output:"arcsin",tex:null,ttype:UNARY,func:true},{input:"Arccos",tag:"mo",output:"arccos",tex:null,ttype:UNARY,func:true},{input:"Arctan",tag:"mo",output:"arctan",tex:null,ttype:UNARY,func:true},{input:"Sinh",tag:"mo",output:"sinh",tex:null,ttype:UNARY,func:true},{input:"Sosh",tag:"mo",output:"cosh",tex:null,ttype:UNARY,func:true},{input:"Tanh",tag:"mo",output:"tanh",tex:null,ttype:UNARY,func:true},{input:"Cot",tag:"mo",output:"cot",tex:null,ttype:UNARY,func:true},{input:"Sec",tag:"mo",output:"sec",tex:null,ttype:UNARY,func:true},{input:"Csc",tag:"mo",output:"csc",tex:null,ttype:UNARY,func:true},{input:"Log",tag:"mo",output:"log",tex:null,ttype:UNARY,func:true},{input:"Ln",tag:"mo",output:"ln",tex:null,ttype:UNARY,func:true},{input:"Abs",tag:"mo",output:"abs",tex:null,ttype:UNARY,func:true},{input:"det",tag:"mo",output:"det",tex:null,ttype:UNARY,func:true},{input:"exp",tag:"mo",output:"exp",tex:null,ttype:UNARY,func:true},{input:"dim",tag:"mo",output:"dim",tex:null,ttype:CONST},{input:"mod",tag:"mo",output:"mod",tex:null,ttype:CONST},{input:"gcd",tag:"mo",output:"gcd",tex:null,ttype:UNARY,func:true},{input:"lcm",tag:"mo",output:"lcm",tex:null,ttype:UNARY,func:true},{input:"lub",tag:"mo",output:"lub",tex:null,ttype:CONST},{input:"glb",tag:"mo",output:"glb",tex:null,ttype:CONST},{input:"min",tag:"mo",output:"min",tex:null,ttype:UNDEROVER},{input:"max",tag:"mo",output:"max",tex:null,ttype:UNDEROVER},{input:"uarr",tag:"mo",output:"\u2191",tex:"uparrow",ttype:CONST},{input:"darr",tag:"mo",output:"\u2193",tex:"downarrow",ttype:CONST},{input:"rarr",tag:"mo",output:"\u2192",tex:"rightarrow",ttype:CONST},{input:"->",tag:"mo",output:"\u2192",tex:"to",ttype:CONST},{input:"|->",tag:"mo",output:"\u21A6",tex:"mapsto",ttype:CONST},{input:"larr",tag:"mo",output:"\u2190",tex:"leftarrow",ttype:CONST},{input:"harr",tag:"mo",output:"\u2194",tex:"leftrightarrow",ttype:CONST},{input:"rArr",tag:"mo",output:"\u21D2",tex:"Rightarrow",ttype:CONST},{input:"lArr",tag:"mo",output:"\u21D0",tex:"Leftarrow",ttype:CONST},{input:"hArr",tag:"mo",output:"\u21D4",tex:"Leftrightarrow",ttype:CONST},AMsqrt,AMroot,AMfrac,AMdiv,AMover,AMsub,AMsup,{input:"Sqrt",tag:"msqrt",output:"sqrt",tex:null,ttype:UNARY},{input:"hat",tag:"mover",output:"\u005E",tex:null,ttype:UNARY,acc:true},{input:"bar",tag:"mover",output:"\u00AF",tex:"overline",ttype:UNARY,acc:true},{input:"vec",tag:"mover",output:"\u2192",tex:null,ttype:UNARY,acc:true},{input:"tilde",tag:"mover",output:"~",tex:null,ttype:UNARY,acc:true},{input:"dot",tag:"mover",output:".",tex:null,ttype:UNARY,acc:true},{input:"ddot",tag:"mover",output:"..",tex:null,ttype:UNARY,acc:true},{input:"ul",tag:"munder",output:"\u0332",tex:"underline",ttype:UNARY,acc:true},AMtext,AMmbox,AMquote,{input:"color",tag:"mstyle",ttype:BINARY},{input:"bb",tag:"mstyle",atname:"fontweight",atval:"bold",output:"bb",tex:null,ttype:UNARY},{input:"mathbf",tag:"mstyle",atname:"fontweight",atval:"bold",output:"mathbf",tex:null,ttype:UNARY},{input:"sf",tag:"mstyle",atname:"fontfamily",atval:"sans-serif",output:"sf",tex:null,ttype:UNARY},{input:"mathsf",tag:"mstyle",atname:"fontfamily",atval:"sans-serif",output:"mathsf",tex:null,ttype:UNARY},{input:"bbb",tag:"mstyle",atname:"mathvariant",atval:"double-struck",output:"bbb",tex:null,ttype:UNARY,codes:AMbbb},{input:"mathbb",tag:"mstyle",atname:"mathvariant",atval:"double-struck",output:"mathbb",tex:null,ttype:UNARY,codes:AMbbb},{input:"cc",tag:"mstyle",atname:"mathvariant",atval:"script",output:"cc",tex:null,ttype:UNARY,codes:AMcal},{input:"mathcal",tag:"mstyle",atname:"mathvariant",atval:"script",output:"mathcal",tex:null,ttype:UNARY,codes:AMcal},{input:"tt",tag:"mstyle",atname:"fontfamily",atval:"monospace",output:"tt",tex:null,ttype:UNARY},{input:"mathtt",tag:"mstyle",atname:"fontfamily",atval:"monospace",output:"mathtt",tex:null,ttype:UNARY},{input:"fr",tag:"mstyle",atname:"mathvariant",atval:"fraktur",output:"fr",tex:null,ttype:UNARY,codes:AMfrk},{input:"mathfrak",tag:"mstyle",atname:"mathvariant",atval:"fraktur",output:"mathfrak",tex:null,ttype:UNARY,codes:AMfrk}];function compareNames(b,a){if(b.input>a.input){return 1}else{return -1}}var AMnames=[];function AMinitSymbols(){var b=[],a;for(a=0;a<AMsymbols.length;a++){if(AMsymbols[a].tex){b[b.length]={input:AMsymbols[a].tex,tag:AMsymbols[a].tag,output:AMsymbols[a].output,ttype:AMsymbols[a].ttype}}}AMsymbols=AMsymbols.concat(b);AMsymbols.sort(compareNames);for(a=0;a<AMsymbols.length;a++){AMnames[a]=AMsymbols[a].input}}var AMmathml="http://www.w3.org/1998/Math/MathML";function AMcreateElementMathML(a){if(isIE){return document.createElement("m:"+a)}else{return document.createElementNS(AMmathml,a)}}function AMcreateMmlNode(a,c){if(isIE){var b=document.createElement("m:"+a)}else{var b=document.createElementNS(AMmathml,a)}b.appendChild(c);return b}function newcommand(a,b){AMsymbols=AMsymbols.concat([{input:a,tag:"mo",output:b,tex:null,ttype:DEFINITION}])}function AMremoveCharsAndBlanks(c,d){var a;if(c.charAt(d)=="\\"&&c.charAt(d+1)!="\\"&&c.charAt(d+1)!=" "){a=c.slice(d+1)}else{a=c.slice(d)}for(var b=0;b<a.length&&a.charCodeAt(b)<=32;b=b+1){}return a.slice(b)}function AMposition(b,e,f){if(f==0){var d,a;f=-1;d=b.length;while(f+1<d){a=(f+d)>>1;if(b[a]<e){f=a}else{d=a}}return d}else{for(var c=f;c<b.length&&b[c]<e;c++){}}return c}function AMgetSymbol(g){var a=0;var b=0;var d;var m;var l;var e="";var f=true;for(var c=1;c<=g.length&&f;c++){m=g.slice(0,c);b=a;a=AMposition(AMnames,m,b);if(a<AMnames.length&&g.slice(0,AMnames[a].length)==AMnames[a]){e=AMnames[a];d=a;c=e.length}f=a<AMnames.length&&g.slice(0,AMnames[a].length)>=AMnames[a]}AMpreviousSymbol=AMcurrentSymbol;if(e!=""){AMcurrentSymbol=AMsymbols[d].ttype;return AMsymbols[d]}AMcurrentSymbol=CONST;a=1;m=g.slice(0,1);var h=true;while("0"<=m&&m<="9"&&a<=g.length){m=g.slice(a,a+1);a++}if(m==decimalsign){m=g.slice(a,a+1);if("0"<=m&&m<="9"){h=false;a++;while("0"<=m&&m<="9"&&a<=g.length){m=g.slice(a,a+1);a++}}}if((h&&a>1)||a>2){m=g.slice(0,a-1);l="mn"}else{a=2;m=g.slice(0,1);l=(("A">m||m>"Z")&&("a">m||m>"z")?"mo":"mi")}if(m=="-"&&AMpreviousSymbol==INFIX){AMcurrentSymbol=INFIX;return{input:m,tag:l,output:m,ttype:UNARY,func:true}}return{input:m,tag:l,output:m,ttype:CONST}}function AMremoveBrackets(b){var a;if(b.nodeName=="mrow"){a=b.firstChild.firstChild.nodeValue;if(a=="("||a=="["||a=="{"){b.removeChild(b.firstChild)}}if(b.nodeName=="mrow"){a=b.lastChild.firstChild.nodeValue;if(a==")"||a=="]"||a=="}"){b.removeChild(b.lastChild)}}}var AMnestingDepth,AMpreviousSymbol,AMcurrentSymbol;function AMparseSexpr(g){var c,b,l,e,k,f=document.createDocumentFragment();g=AMremoveCharsAndBlanks(g,0);c=AMgetSymbol(g);if(c==null||c.ttype==RIGHTBRACKET&&AMnestingDepth>0){return[null,g]}if(c.ttype==DEFINITION){g=c.output+AMremoveCharsAndBlanks(g,c.input.length);c=AMgetSymbol(g)}switch(c.ttype){case UNDEROVER:case CONST:g=AMremoveCharsAndBlanks(g,c.input.length);return[AMcreateMmlNode(c.tag,document.createTextNode(c.output)),g];case LEFTBRACKET:AMnestingDepth++;g=AMremoveCharsAndBlanks(g,c.input.length);l=AMparseExpr(g,true);AMnestingDepth--;if(typeof c.invisible=="boolean"&&c.invisible){b=AMcreateMmlNode("mrow",l[0])}else{b=AMcreateMmlNode("mo",document.createTextNode(c.output));b=AMcreateMmlNode("mrow",b);b.appendChild(l[0])}return[b,l[1]];case TEXT:if(c!=AMquote){g=AMremoveCharsAndBlanks(g,c.input.length)}if(g.charAt(0)=="{"){e=g.indexOf("}")}else{if(g.charAt(0)=="("){e=g.indexOf(")")}else{if(g.charAt(0)=="["){e=g.indexOf("]")}else{if(c==AMquote){e=g.slice(1).indexOf('"')+1}else{e=0}}}}if(e==-1){e=g.length}k=g.slice(1,e);if(k.charAt(0)==" "){b=AMcreateElementMathML("mspace");b.setAttribute("width","1ex");f.appendChild(b)}f.appendChild(AMcreateMmlNode(c.tag,document.createTextNode(k)));if(k.charAt(k.length-1)==" "){b=AMcreateElementMathML("mspace");b.setAttribute("width","1ex");f.appendChild(b)}g=AMremoveCharsAndBlanks(g,e+1);return[AMcreateMmlNode("mrow",f),g];case UNARY:g=AMremoveCharsAndBlanks(g,c.input.length);l=AMparseSexpr(g);if(l[0]==null){return[AMcreateMmlNode(c.tag,document.createTextNode(c.output)),g]}if(typeof c.func=="boolean"&&c.func){k=g.charAt(0);if(k=="^"||k=="_"||k=="/"||k=="|"||k==","){return[AMcreateMmlNode(c.tag,document.createTextNode(c.output)),g]}else{b=AMcreateMmlNode("mrow",AMcreateMmlNode(c.tag,document.createTextNode(c.output)));b.appendChild(l[0]);return[b,l[1]]}}AMremoveBrackets(l[0]);if(c.input=="sqrt"){return[AMcreateMmlNode(c.tag,l[0]),l[1]]}else{if(typeof c.acc=="boolean"&&c.acc){b=AMcreateMmlNode(c.tag,l[0]);b.setAttribute("accent","true");b.appendChild(AMcreateMmlNode("mo",document.createTextNode(c.output)));return[b,l[1]]}else{if(!isIE&&typeof c.codes!="undefined"){for(e=0;e<l[0].childNodes.length;e++){if(l[0].childNodes[e].nodeName=="mi"||l[0].nodeName=="mi"){k=(l[0].nodeName=="mi"?l[0].firstChild.nodeValue:l[0].childNodes[e].firstChild.nodeValue);var h=[];for(var d=0;d<k.length;d++){if(k.charCodeAt(d)>64&&k.charCodeAt(d)<91){h=h+c.codes[k.charCodeAt(d)-65]}else{h=h+k.charAt(d)}}if(l[0].nodeName=="mi"){l[0]=AMcreateElementMathML("mo").appendChild(document.createTextNode(h))}else{l[0].replaceChild(AMcreateElementMathML("mo").appendChild(document.createTextNode(h)),l[0].childNodes[e])}}}}b=AMcreateMmlNode(c.tag,l[0]);b.setAttribute(c.atname,c.atval);return[b,l[1]]}}case BINARY:g=AMremoveCharsAndBlanks(g,c.input.length);l=AMparseSexpr(g);if(l[0]==null){return[AMcreateMmlNode("mo",document.createTextNode(c.input)),g]}AMremoveBrackets(l[0]);var a=AMparseSexpr(l[1]);if(a[0]==null){return[AMcreateMmlNode("mo",document.createTextNode(c.input)),g]}AMremoveBrackets(a[0]);if(c.input=="color"){if(g.charAt(0)=="{"){e=g.indexOf("}")}else{if(g.charAt(0)=="("){e=g.indexOf(")")}else{if(g.charAt(0)=="["){e=g.indexOf("]")}}}k=g.slice(1,e);b=AMcreateMmlNode(c.tag,a[0]);b.setAttribute("color",k);return[b,a[1]]}if(c.input=="root"||c.input=="stackrel"){f.appendChild(a[0])}f.appendChild(l[0]);if(c.input=="frac"){f.appendChild(a[0])}return[AMcreateMmlNode(c.tag,f),a[1]];case INFIX:g=AMremoveCharsAndBlanks(g,c.input.length);return[AMcreateMmlNode("mo",document.createTextNode(c.output)),g];case SPACE:g=AMremoveCharsAndBlanks(g,c.input.length);b=AMcreateElementMathML("mspace");b.setAttribute("width","1ex");f.appendChild(b);f.appendChild(AMcreateMmlNode(c.tag,document.createTextNode(c.output)));b=AMcreateElementMathML("mspace");b.setAttribute("width","1ex");f.appendChild(b);return[AMcreateMmlNode("mrow",f),g];case LEFTRIGHT:AMnestingDepth++;g=AMremoveCharsAndBlanks(g,c.input.length);l=AMparseExpr(g,false);AMnestingDepth--;var k="";if(l[0].lastChild!=null){k=l[0].lastChild.firstChild.nodeValue}if(k=="|"){b=AMcreateMmlNode("mo",document.createTextNode(c.output));b=AMcreateMmlNode("mrow",b);b.appendChild(l[0]);return[b,l[1]]}else{b=AMcreateMmlNode("mo",document.createTextNode(c.output));b=AMcreateMmlNode("mrow",b);return[b,g]}default:g=AMremoveCharsAndBlanks(g,c.input.length);return[AMcreateMmlNode(c.tag,document.createTextNode(c.output)),g]}}function AMparseIexpr(g){var e,h,f,d,a,c;g=AMremoveCharsAndBlanks(g,0);h=AMgetSymbol(g);a=AMparseSexpr(g);d=a[0];g=a[1];e=AMgetSymbol(g);if(e.ttype==INFIX&&e.input!="/"){g=AMremoveCharsAndBlanks(g,e.input.length);a=AMparseSexpr(g);if(a[0]==null){a[0]=AMcreateMmlNode("mo",document.createTextNode("\u25A1"))}else{AMremoveBrackets(a[0])}g=a[1];if(e.input=="_"){f=AMgetSymbol(g);c=(h.ttype==UNDEROVER);if(f.input=="^"){g=AMremoveCharsAndBlanks(g,f.input.length);var b=AMparseSexpr(g);AMremoveBrackets(b[0]);g=b[1];d=AMcreateMmlNode((c?"munderover":"msubsup"),d);d.appendChild(a[0]);d.appendChild(b[0]);d=AMcreateMmlNode("mrow",d)}else{d=AMcreateMmlNode((c?"munder":"msub"),d);d.appendChild(a[0])}}else{d=AMcreateMmlNode(e.tag,d);d.appendChild(a[0])}}return[d,g]}function AMparseExpr(l,h){var r,o,e,u,y=[],f=document.createDocumentFragment();do{l=AMremoveCharsAndBlanks(l,0);e=AMparseIexpr(l);o=e[0];l=e[1];r=AMgetSymbol(l);if(r.ttype==INFIX&&r.input=="/"){l=AMremoveCharsAndBlanks(l,r.input.length);e=AMparseIexpr(l);if(e[0]==null){e[0]=AMcreateMmlNode("mo",document.createTextNode("\u25A1"))}else{AMremoveBrackets(e[0])}l=e[1];AMremoveBrackets(o);o=AMcreateMmlNode(r.tag,o);o.appendChild(e[0]);f.appendChild(o);r=AMgetSymbol(l)}else{if(o!=undefined){f.appendChild(o)}}}while((r.ttype!=RIGHTBRACKET&&(r.ttype!=LEFTRIGHT||h)||AMnestingDepth==0)&&r!=null&&r.output!="");if(r.ttype==RIGHTBRACKET||r.ttype==LEFTRIGHT){var v=f.childNodes.length;if(v>0&&f.childNodes[v-1].nodeName=="mrow"){var x=f.childNodes[v-1].lastChild.firstChild.nodeValue;if(x==")"||x=="]"){var b=f.childNodes[v-1].firstChild.firstChild.nodeValue;if(b=="("&&x==")"&&r.output!="}"||b=="["&&x=="]"){var c=[];var s=true;var p=f.childNodes.length;for(u=0;s&&u<p;u=u+2){c[u]=[];o=f.childNodes[u];if(s){s=o.nodeName=="mrow"&&(u==p-1||o.nextSibling.nodeName=="mo"&&o.nextSibling.firstChild.nodeValue==",")&&o.firstChild.firstChild.nodeValue==b&&o.lastChild.firstChild.nodeValue==x}if(s){for(var t=0;t<o.childNodes.length;t++){if(o.childNodes[t].firstChild.nodeValue==","){c[u][c[u].length]=t}}}if(s&&u>1){s=c[u].length==c[u-2].length}}if(s){var d,a,g,q,w=document.createDocumentFragment();for(u=0;u<p;u=u+2){d=document.createDocumentFragment();a=document.createDocumentFragment();o=f.firstChild;g=o.childNodes.length;q=0;o.removeChild(o.firstChild);for(t=1;t<g-1;t++){if(typeof c[u][q]!="undefined"&&t==c[u][q]){o.removeChild(o.firstChild);d.appendChild(AMcreateMmlNode("mtd",a));q++}else{a.appendChild(o.firstChild)}}d.appendChild(AMcreateMmlNode("mtd",a));if(f.childNodes.length>2){f.removeChild(f.firstChild);f.removeChild(f.firstChild)}w.appendChild(AMcreateMmlNode("mtr",d))}o=AMcreateMmlNode("mtable",w);if(typeof r.invisible=="boolean"&&r.invisible){o.setAttribute("columnalign","left")}f.replaceChild(o,f.firstChild)}}}}l=AMremoveCharsAndBlanks(l,r.input.length);if(typeof r.invisible!="boolean"||!r.invisible){o=AMcreateMmlNode("mo",document.createTextNode(r.output));f.appendChild(o)}}return[f,l]}function AMparseMath(d){var a,c=AMcreateElementMathML("mstyle");if(mathcolor!=""){c.setAttribute("mathcolor",mathcolor)}if(displaystyle){c.setAttribute("displaystyle","true")}if(mathfontfamily!=""){c.setAttribute("fontfamily",mathfontfamily)}AMnestingDepth=0;d=d.replace(/&nbsp;/g,"");d=d.replace(/&gt;/g,">");d=d.replace(/&lt;/g,"<");c.appendChild(AMparseExpr(d.replace(/^\s+/g,""),false)[0]);c=AMcreateMmlNode("math",c);if(showasciiformulaonhover){c.setAttribute("title",d.replace(/\s+/g," "))}if(mathfontfamily!=""&&(isIE||mathfontfamily!="serif")){var b=AMcreateElementXHTML("font");b.setAttribute("face",mathfontfamily);b.appendChild(c);return b}return c}function AMstrarr2docFrag(a,d){var g=document.createDocumentFragment();var f=false;for(var e=0;e<a.length;e++){if(f){g.appendChild(AMparseMath(a[e]))}else{var b=(d?a[e].split("\n\n"):[a[e]]);g.appendChild(AMcreateElementXHTML("span").appendChild(document.createTextNode(b[0])));for(var c=1;c<b.length;c++){g.appendChild(AMcreateElementXHTML("p"));g.appendChild(AMcreateElementXHTML("span").appendChild(document.createTextNode(b[c])))}}f=!f}return g}function AMprocessNodeR(b,g){var j,h,e,a,d;if(b.childNodes.length==0){if((b.nodeType!=8||g)&&b.parentNode.nodeName!="form"&&b.parentNode.nodeName!="FORM"&&b.parentNode.nodeName!="textarea"&&b.parentNode.nodeName!="TEXTAREA"&&b.parentNode.nodeName!="pre"&&b.parentNode.nodeName!="PRE"){h=b.nodeValue;if(!(h==null)){h=h.replace(/\r\n\r\n/g,"\n\n");if(doubleblankmathdelimiter){h=h.replace(/\x20\x20\./g," "+AMdelimiter1+".");h=h.replace(/\x20\x20,/g," "+AMdelimiter1+",");h=h.replace(/\x20\x20/g," "+AMdelimiter1+" ")}h=h.replace(/\x20+/g," ");h=h.replace(/\s*\r\n/g," ");j=false;h=h.replace(new RegExp(AMescape1,"g"),function(i){j=true;return"AMescape1"});e=h.split(AMdelimiter1);for(d=0;d<e.length;d++){e[d]=e[d].replace(/AMescape1/g,AMdelimiter1)}if(e.length>1||j){if(checkForMathML){checkForMathML=false;var c=AMisMathMLavailable();AMnoMathML=c!=null;if(AMnoMathML&&notifyIfNoMathML){if(alertIfNoMathML){alert("To view the ASCIIMathML notation use Internet Explorer 6 +\nMathPlayer (free from www.dessci.com)\n                or Firefox/Mozilla/Netscape")}else{AMbody.insertBefore(c,AMbody.childNodes[0])}}}if(!AMnoMathML){a=AMstrarr2docFrag(e,b.nodeType==8);var f=a.childNodes.length;b.parentNode.replaceChild(a,b);return f-1}else{return 0}}}}else{return 0}}else{if(b.nodeName!="math"){for(d=0;d<b.childNodes.length;d++){d+=AMprocessNodeR(b.childNodes[d],g)}}}return 0}function AMprocessNode(g,b,e){var f,a;if(e!=null){f=document.getElementsByTagName("span");for(var c=0;c<f.length;c++){if(f[c].className=="AM"){AMprocessNodeR(f[c],b)}}}else{try{a=g.innerHTML}catch(d){}if(a==null||a.indexOf(AMdelimiter1)!=-1){AMprocessNodeR(g,b)}}if(isIE){f=document.getElementsByTagName("math");for(var c=0;c<f.length;c++){f[c].update()}}}var AMbody;var AMnoMathML=false,AMtranslated=false;function translate(a){if(!AMtranslated){AMtranslated=true;AMbody=document.getElementsByTagName("body")[0];AMprocessNode(AMbody,false,a)}}AMinitSymbols();if(isIE){document.write('<object id="mathplayer"  classid="clsid:32F66A20-7614-11D4-BD11-00104BD3F987"></object>');document.write('<?import namespace="m" implementation="#mathplayer"?>')}if(typeof(initstack)!="undefined"){initstack.push(translate)}else{function generic(){translate()}if(typeof window.addEventListener!="undefined"){window.addEventListener("load",generic,false)}else{if(typeof document.addEventListener!="undefined"){document.addEventListener("load",generic,false)}else{if(typeof window.attachEvent!="undefined"){window.attachEvent("onload",generic)}else{if(typeof window.onload=="function"){var existing=onload;window.onload=function(){existing();generic()}}else{window.onload=generic}}}}}if(checkForMathML){checkForMathML=false;var nd=AMisMathMLavailable();AMnoMathML=nd!=null};