import { chromium } from 'playwright';
const base='http://127.0.0.1:8000';
const pages=[
 ['/our-services','our-services'],['/activities','activities'],['/tour-packages','tour-packages'],
 ['/uaevisa','uaevisa'],['/e-visa','e-visa'],['/esim','esim'],['/contact-us','contact-us'],
 ['/hajj-umrah','hajj-umrah'],['/fifa-world-cup-2026','fifa'],['/ourstory','ourstory']
];
const b=await chromium.launch();
for(const [path,name] of pages){
 try{
  const ctx=await b.newContext({viewport:{width:1366,height:860}});
  const p=await ctx.newPage();
  await p.goto(base+path,{waitUntil:'domcontentloaded'});
  await p.evaluate(()=>localStorage.setItem('gt-theme','light'));
  await p.goto(base+path,{waitUntil:'networkidle'}).catch(()=>{});
  await p.waitForTimeout(900);
  await p.evaluate(()=>{['#gotripsLeadPopupOverlay','.gotrips-lead-popup-overlay','.modal-backdrop'].forEach(s=>document.querySelectorAll(s).forEach(e=>e.style.display='none'));});
  await p.screenshot({path:`scratch/p-${name}.png`});
  await ctx.close();
  console.log('ok',name);
 }catch(e){console.log('ERR',name,e.message.slice(0,60));}
}
await b.close();
