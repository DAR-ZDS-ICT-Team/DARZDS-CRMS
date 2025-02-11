import{o as n,e as i,Q as T,d as x,C as z,n as u,a as e,f as g,t as y,l as M,y as E,D as N,p as k,r as h,k as j,u as f,q as B,E as A,b as l,w as a,G as O,c as _,m as $,Z as F,g as d,F as I,z as V,A as R,O as U}from"./app-Q9sJr0ix.js";import{_ as q}from"./dar-logo-BEVYNDow.js";import{_ as P}from"./_plugin-vue_export-helper-DlAUqK2U.js";const G={},H={class:"w-16 h-16",viewBox:"0 0 48 48",fill:"none",src:q};function Q(r,o){return n(),i("img",H)}const Z=P(G,[["render",Q]]),J={class:"max-w-screen-xl mx-auto py-2 px-3 sm:px-6 lg:px-8"},K={class:"flex items-center justify-between flex-wrap"},W={class:"w-0 flex-1 flex items-center min-w-0"},X={key:0,class:"h-5 w-5 text-white",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},Y=e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"},null,-1),ee=[Y],te={key:1,class:"h-5 w-5 text-white",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},se=e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"},null,-1),oe=[se],ne={class:"ms-3 font-medium text-sm text-white truncate"},re={class:"shrink-0 sm:ms-3"},ae=e("svg",{class:"h-5 w-5 text-white",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M6 18L18 6M6 6l12 12"})],-1),ie=[ae],le={__name:"Banner",setup(r){const o=T(),s=x(!0),t=x("success"),c=x("");return z(async()=>{var v,p;t.value=((v=o.props.jetstream.flash)==null?void 0:v.bannerStyle)||"success",c.value=((p=o.props.jetstream.flash)==null?void 0:p.banner)||"",s.value=!0}),(v,p)=>(n(),i("div",null,[s.value&&c.value?(n(),i("div",{key:0,class:u({"bg-indigo-500":t.value=="success","bg-red-700":t.value=="danger"})},[e("div",J,[e("div",K,[e("div",W,[e("span",{class:u(["flex p-2 rounded-lg",{"bg-indigo-600":t.value=="success","bg-red-600":t.value=="danger"}])},[t.value=="success"?(n(),i("svg",X,ee)):g("",!0),t.value=="danger"?(n(),i("svg",te,oe)):g("",!0)],2),e("p",ne,y(c.value),1)]),e("div",re,[e("button",{type:"button",class:u(["-me-1 flex p-2 rounded-md focus:outline-none sm:-me-2 transition",{"hover:bg-indigo-600 focus:bg-indigo-600":t.value=="success","hover:bg-red-600 focus:bg-red-600":t.value=="danger"}]),"aria-label":"Dismiss",onClick:p[0]||(p[0]=M(m=>s.value=!1,["prevent"]))},ie,2)])])])],2)):g("",!0)]))}},de={class:"relative"},D={__name:"Dropdown",props:{align:{type:String,default:"right"},width:{type:String,default:"48"},contentClasses:{type:Array,default:()=>["py-1","bg-white"]}},setup(r){const o=r;let s=x(!1);const t=p=>{s.value&&p.key==="Escape"&&(s.value=!1)};E(()=>document.addEventListener("keydown",t)),N(()=>document.removeEventListener("keydown",t));const c=k(()=>({48:"w-48"})[o.width.toString()]),v=k(()=>o.align==="left"?"ltr:origin-top-left rtl:origin-top-right start-0":o.align==="right"?"ltr:origin-top-right rtl:origin-top-left end-0":"origin-top");return(p,m)=>(n(),i("div",de,[e("div",{onClick:m[0]||(m[0]=L=>j(s)?s.value=!f(s):s=!f(s))},[h(p.$slots,"trigger")]),B(e("div",{class:"fixed inset-0 z-40",onClick:m[1]||(m[1]=L=>j(s)?s.value=!1:s=!1)},null,512),[[A,f(s)]]),l(O,{"enter-active-class":"transition ease-out duration-200","enter-from-class":"transform opacity-0 scale-95","enter-to-class":"transform opacity-100 scale-100","leave-active-class":"transition ease-in duration-75","leave-from-class":"transform opacity-100 scale-100","leave-to-class":"transform opacity-0 scale-95"},{default:a(()=>[B(e("div",{class:u(["absolute z-50 mt-2 rounded-md shadow-lg",[c.value,v.value]]),style:{display:"none"},onClick:m[2]||(m[2]=L=>j(s)?s.value=!1:s=!1)},[e("div",{class:u(["rounded-md ring-1 ring-black ring-opacity-5",r.contentClasses])},[h(p.$slots,"content")],2)],2),[[A,f(s)]])]),_:3})]))}},ue={key:0,type:"submit",class:"block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"},ce=["href"],C={__name:"DropdownLink",props:{href:String,as:String},setup(r){return(o,s)=>(n(),i("div",null,[r.as=="button"?(n(),i("button",ue,[h(o.$slots,"default")])):r.as=="a"?(n(),i("a",{key:1,href:r.href,class:"block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"},[h(o.$slots,"default")],8,ce)):(n(),_(f($),{key:2,href:r.href,class:"block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"},{default:a(()=>[h(o.$slots,"default")]),_:3},8,["href"]))]))}},S={__name:"NavLink",props:{href:String,active:Boolean},setup(r){const o=r,s=k(()=>o.active?"inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out":"inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out");return(t,c)=>(n(),_(f($),{href:r.href,class:u(s.value)},{default:a(()=>[h(t.$slots,"default")]),_:3},8,["href","class"]))}},w={__name:"ResponsiveNavLink",props:{active:Boolean,href:String,as:String},setup(r){const o=r,s=k(()=>o.active?"block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 text-start text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out":"block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out");return(t,c)=>(n(),i("div",null,[r.as=="button"?(n(),i("button",{key:0,class:u([s.value,"w-full text-start"])},[h(t.$slots,"default")],2)):(n(),_(f($),{key:1,href:r.href,class:u(s.value)},{default:a(()=>[h(t.$slots,"default")]),_:3},8,["href","class"]))]))}},b=r=>(V("data-v-b4a1c68b"),r=r(),R(),r),pe={class:"min-h-screen bg-gray-100"},he={class:"bg-white border-b border-gray-100"},ge={class:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"},fe={class:"flex justify-between h-16"},me={class:"flex"},ve={class:"shrink-0 flex items-center"},_e={class:"hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"},be={class:"hidden sm:flex sm:items-center sm:ms-6"},ye={class:"ms-3 relative"},xe={class:"inline-flex rounded-md"},we={type:"button",class:"inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"},ke=b(()=>e("svg",{class:"ms-2 -me-0.5 h-4 w-4",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"})],-1)),$e={class:"ms-3 relative"},je={key:0,class:"flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"},Ce=["src","alt"],Se={key:1,class:"inline-flex rounded-md"},Me={type:"button",class:"inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"},Le=b(()=>e("svg",{class:"ms-2 -me-0.5 h-4 w-4",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M19.5 8.25l-7.5 7.5-7.5-7.5"})],-1)),Be=b(()=>e("div",{class:"block px-4 py-2 text-xs text-gray-400"}," Manage Account ",-1)),Ae=b(()=>e("div",{class:"border-t border-gray-200"},null,-1)),De={class:"-me-2 flex items-center sm:hidden"},Pe={class:"h-6 w-6",stroke:"currentColor",fill:"none",viewBox:"0 0 24 24"},Te={class:"pt-2 pb-3 space-y-1"},ze={class:"pt-4 pb-1 border-t border-gray-200"},Ee={class:"flex items-center px-4"},Ne={key:0,class:"shrink-0 me-3"},Oe=["src","alt"],Fe={class:"font-medium text-base text-gray-800"},Ie={class:"font-medium text-sm text-gray-500"},Ve={class:"mt-3 space-y-1"},Re=b(()=>e("div",{class:"border-t border-gray-200"},null,-1)),Ue=b(()=>e("div",{class:"block px-4 py-2 text-xs text-gray-400"}," Manage Team ",-1)),qe={key:0,class:"bg-white shadow"},Ge={class:"max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8"},He={__name:"AppLayout",props:{title:String,auth:Object},setup(r){const o=x(!1),s=()=>{U.post("/logout")};return(t,c)=>(n(),i("div",null,[l(f(F),{title:r.title},null,8,["title"]),l(le),e("div",pe,[e("nav",he,[e("div",ge,[e("div",fe,[e("div",me,[e("div",ve,[l(f($),{href:"/dashboard"},{default:a(()=>[l(Z,{class:"block h-9 w-auto"})]),_:1})]),e("div",_e,[l(S,{href:"/dashboard",active:"/dashboard"},{default:a(()=>[d(" Dashboard ")]),_:1}),l(S,{href:"/division_sections",active:"/service_units"},{default:a(()=>[d(" Division Units ")]),_:1}),l(S,{href:"/libraries",active:"/libraries"},{default:a(()=>[d(" Libraries ")]),_:1})])]),e("div",be,[e("div",ye,[t.$page.props.jetstream.hasTeamFeatures?(n(),_(D,{key:0,align:"right",width:"60"},{trigger:a(()=>[e("span",xe,[e("button",we,[d(y(t.$page.props.auth.user.current_team.name)+" ",1),ke])])]),_:1})):g("",!0)]),e("div",$e,[l(D,{align:"right",width:"48"},{trigger:a(()=>[t.$page.props.jetstream.managesProfilePhotos?(n(),i("button",je,[e("img",{class:"h-8 w-8 rounded-full object-cover",src:t.$page.props.auth.user.profile_photo_url,alt:t.$page.props.auth.user.name},null,8,Ce)])):(n(),i("span",Se,[e("button",Me,[d(y(t.$page.props.auth.user.name)+" ",1),Le])]))]),content:a(()=>[Be,l(C,{href:"/profile"},{default:a(()=>[d(" Profile ")]),_:1}),t.$page.props.jetstream.hasApiFeatures?(n(),_(C,{key:0,href:"/api-tokens.index"},{default:a(()=>[d(" API Tokens ")]),_:1})):g("",!0),Ae,e("form",{onSubmit:M(s,["prevent"])},[l(C,{as:"button"},{default:a(()=>[d(" Log Out ")]),_:1})],32)]),_:1})])]),e("div",De,[e("button",{class:"inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out",onClick:c[0]||(c[0]=v=>o.value=!o.value)},[(n(),i("svg",Pe,[e("path",{class:u({hidden:o.value,"inline-flex":!o.value}),"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M4 6h16M4 12h16M4 18h16"},null,2),e("path",{class:u({hidden:!o.value,"inline-flex":o.value}),"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M6 18L18 6M6 6l12 12"},null,2)]))])])])]),e("div",{class:u([{block:o.value,hidden:!o.value},"sm:hidden"])},[e("div",Te,[l(w,{href:"/dashboard",active:"/dashboard"},{default:a(()=>[d(" Dashboard ")]),_:1})]),e("div",ze,[e("div",Ee,[t.$page.props.jetstream.managesProfilePhotos?(n(),i("div",Ne,[e("img",{class:"h-10 w-10 rounded-full object-cover",src:t.$page.props.auth.user.profile_photo_url,alt:t.$page.props.auth.user.name},null,8,Oe)])):g("",!0),e("div",null,[e("div",Fe,y(t.$page.props.auth.user.name),1),e("div",Ie,y(t.$page.props.auth.user.email),1)])]),e("div",Ve,[l(w,{href:"/profile",active:"/profile"},{default:a(()=>[d(" Profile ")]),_:1}),t.$page.props.jetstream.hasApiFeatures?(n(),_(w,{key:0,href:"/api-tokens.index",active:"/api-tokens.index"},{default:a(()=>[d(" API Tokens ")]),_:1})):g("",!0),e("form",{method:"POST",onSubmit:M(s,["prevent"])},[l(w,{as:"button"},{default:a(()=>[d(" Log Out ")]),_:1})],32),t.$page.props.jetstream.hasTeamFeatures?(n(),i(I,{key:1},[Re,Ue],64)):g("",!0)])])],2)]),t.$slots.header?(n(),i("header",qe,[e("div",Ge,[h(t.$slots,"header",{},void 0,!0)])])):g("",!0),e("main",null,[h(t.$slots,"default",{},void 0,!0)])])]))}},Ke=P(He,[["__scopeId","data-v-b4a1c68b"]]);export{Ke as A};
