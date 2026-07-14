import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext();
const p = await ctx.newPage();
await p.goto('http://127.0.0.1:8000/', {waitUntil:'domcontentloaded'});
await p.evaluate(()=>localStorage.setItem('gt-theme','light'));
await p.goto('http://127.0.0.1:8000/', {waitUntil:'networkidle'}).catch(()=>{});
const r = await p.evaluate(()=>{
  const g = (sel)=>{const e=document.querySelector(sel); if(!e) return sel+': MISSING'; const c=getComputedStyle(e); return sel+': bg='+c.backgroundColor+' img='+(c.backgroundImage||'').slice(0,40);};
  return [g('.services-section'), g('.service-card'), g('html'), g('.home-fifa')].join('\n');
});
console.log(r);
await b.close();
