(window.webpackJsonp=window.webpackJsonp||[]).push([[3],{286:function(t,e,n){"use strict";n.r(e);n(33),n(40),n(29),n(41),n(58),n(34),n(59);var r=n(25),c=(n(15),n(57),n(56)),o=n(285);function l(object,t){var e=Object.keys(object);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(object);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(object,t).enumerable}))),e.push.apply(e,n)}return e}var v={name:"ExchangeRateComponent",components:{Multiselect:n.n(o).a},computed:function(t){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?l(Object(source),!0).forEach((function(e){Object(r.a)(t,e,source[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(source)):l(Object(source)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(source,e))}))}return t}({},Object(c.b)({currencies:"currency/currencies"})),data:function(){return{toConvert:[],conversions:null,submitting:!1}},methods:{fetchCurrencyConversions:function(){var t=this;0!==this.toConvert.length&&(this.submitting=!0,this.$axios.post("exchange-rates",{currencies:this.toConvert.map((function(t){return t.code}))}).then((function(e){t.conversions=e.data})).finally((function(){t.submitting=!1})))}},mounted:function(){var t=this;this.currencies||this.$axios.get("currencies").then((function(e){t.$store.dispatch("currency/store",e.data)}))}},m=v,f=n(22),component=Object(f.a)(m,(function(){var t=this,e=t._self._c;return e("div",{staticClass:"overflow-hidden bg-white shadow sm:rounded-lg"},[e("div",{staticClass:"bg-white px-6 py-5 my-4"},[e("div",{staticClass:"-ml-4 -mt-2 flex flex-wrap items-center justify-between sm:flex-nowrap"},[t._m(0),t._v(" "),t.currencies?e("div",{staticClass:"w-1/2"},[e("label",{staticClass:"sr-only"},[t._v("Select currencies to compare")]),t._v(" "),e("multiselect",{staticClass:"text-gray-700",attrs:{options:t.currencies,multiple:!0,"close-on-select":!1,"clear-on-select":!1,"preserve-search":!0,placeholder:"Select currencies to compare",label:"code","track-by":"name","max-height":255,max:5,searchable:!t.submitting},on:{close:t.fetchCurrencyConversions},scopedSlots:t._u([{key:"option",fn:function(n){var option=n.option;return[e("strong",[t._v(t._s(option.code))]),t._v(" "),e("small",{staticClass:"text-sm ml-3"},[t._v(t._s(option.name))])]}}],null,!1,1797006662),model:{value:t.toConvert,callback:function(e){t.toConvert=e},expression:"toConvert"}})],1):t._e()])]),t._v(" "),e("div",{staticClass:"border-t h-64 border-gray-200"},[t.conversions?e("table",{staticClass:"min-w-full divide-y divide-gray-300"},[t._m(1),t._v(" "),e("tbody",{staticClass:"divide-y divide-gray-200 bg-white"},t._l(t.conversions,(function(n,r){return e("tr",[e("td",{staticClass:"whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6"},[t._v("\n                        "+t._s(n.currency)+"\n                    ")]),t._v(" "),e("td",{staticClass:"whitespace-nowrap px-3 py-4 text-sm text-gray-500"},[t._v("\n                        "+t._s(n.rate)+"\n                    ")])])})),0)]):e("div",{staticClass:"flex h-full flex-1 items-center justify-center text-gray-400 font-light"},[t._v("\n            Please select currencies to view exchange rate data\n        ")])])])}),[function(){var t=this,e=t._self._c;return e("div",{staticClass:"ml-4 mt-2"},[e("h3",{staticClass:"text-lg font-medium leading-6 text-gray-900"},[t._v("Live Conversion")]),t._v(" "),e("p",{staticClass:"mt-1 max-w-2xl text-sm text-gray-400"},[t._v("Select up to 5 currencies")])])},function(){var t=this,e=t._self._c;return e("thead",{staticClass:"bg-gray-50"},[e("tr",[e("th",{staticClass:"py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6",attrs:{scope:"col"}},[t._v("\n                        Currency\n                    ")]),t._v(" "),e("th",{staticClass:"px-3 py-3.5 text-left text-sm font-semibold text-gray-900",attrs:{scope:"col"}},[t._v("\n                        Exchange Rate\n                    ")])])])}],!1,null,null,null);e.default=component.exports}}]);