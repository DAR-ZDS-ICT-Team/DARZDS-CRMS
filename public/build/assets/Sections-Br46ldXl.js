import{_ as S}from"./dar-logo-BEVYNDow.js";import{j as i,o as d,e as m,b as e,u as f,a as s,g as n,t as u,f as j,w as t,F as g,Z as C,h as N,c as O,m as b,z as B,A,O as I}from"./app-DXAJS-Z1.js";import{A as V}from"./aos-TYIyTShQ.js";import{_ as z}from"./_plugin-vue_export-helper-DlAUqK2U.js";const D=o=>(B("data-v-0642882c"),o=o(),A(),o),R={"data-aos":"fade-down","data-aos-duration":"500","data-aos-delay":"500",style:{"backdrop-filter":"blur(2px)"},class:"bg-white dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600"},F={class:"max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4"},L={href:"/",class:"flex items-center space-x-3 rtl:space-x-reverse"},$=D(()=>s("img",{src:S,class:"h-8",alt:"DAR Logo"},null,-1)),E={class:"self-center text-2xl font-semibold whitespace-nowrap"},M={key:0},T={class:"w-full border bg-primary"},Z={style:{height:"150px"},class:"card mx-5 max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"},q={class:"mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white"},G={__name:"Sections",props:{division_sections:Object,office_id:Number,office:Object,division_id:Number,division:Object,sub_sections:Object},setup(o){V.init();const v=async(r,a,c)=>{x(a,c,r)},x=async(r,a,c)=>{I.get("/divisions/csf/section/sub-sections?office_id="+r+"&division_id="+a+"&section_id="+c)},h=async()=>{window.history.back()};return(r,a)=>{const c=i("v-card-title"),p=i("v-col"),l=i("v-row"),y=i("v-icon"),w=i("v-btn"),k=i("v-container");return d(),m(g,null,[e(f(C),{title:"Sections"}),s("nav",R,[s("div",F,[s("a",L,[$,s("span",E,[n("DAR "),o.office?(d(),m("span",M,u(o.office.code),1)):j("",!0),n(" Customer Relation Management System")])])])]),e(k,{"fill-height":""},{default:t(()=>[e(l,{class:"mx-15",style:{"margin-top":"100px"}},{default:t(()=>[e(p,null,{default:t(()=>[s("div",T,[e(c,{class:"text-center"},{default:t(()=>[n(u(o.division.division_name),1)]),_:1})])]),_:1})]),_:1}),e(l,{class:"mx-15 mt-5",align:"center",justify:"center"},{default:t(()=>[(d(!0),m(g,null,N(o.division_sections,_=>(d(),O(p,{cols:"12",sm:"4",md:"4",lg:"4"},{default:t(()=>[e(f(b),{onClick:H=>v(_.id,o.office_id,o.division_id)},{default:t(()=>[s("div",Z,[e(y,{color:"green",size:"x-large",class:"p-3"},{default:t(()=>[n("mdi-check-circle")]),_:1}),s("h5",q,u(_.section_name),1)])]),_:2},1032,["onClick"])]),_:2},1024))),256))]),_:1}),e(l,null,{default:t(()=>[e(f(b),{onClick:a[0]||(a[0]=_=>h())},{default:t(()=>[e(w,{"prepend-icon":"mdi-arrow-left",style:{"margin-left":"120px"}},{default:t(()=>[n("Back")]),_:1})]),_:1})]),_:1})]),_:1})],64)}}},U=z(G,[["__scopeId","data-v-0642882c"]]);export{U as default};
