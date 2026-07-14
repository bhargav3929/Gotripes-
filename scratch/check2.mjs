import { chromium } from 'playwright';
const b=await chromium.launch();const ctx=await b.newContext();const p=await ctx.newPage();
await p.goto('http://127.0.0.1:8000/',{waitUntil:'domcontentloaded'});
await p.evaluate(()=>localStorage.setItem('gt-theme','light'));
await p.goto('http://127.0.0.1:8000/',{waitUntil:'networkidle'}).catch(()=>{});
const r=await p.evaluate(()=>{
  const out=[];
  const bd=document.body; out.push('body bg='+getComputedStyle(bd).backgroundColor);
  const t=[...document.querySelectorAll('h1.premium-section-title, .premium-section-title')].find(e=>/TRIPS/i.test(e.textContent));
  if(t){ let el=t.closest('section')||t.parentElement; for(let i=0;i<4 && el;i++){ const c=getComputedStyle(el); out.push('TRIPS ancestor['+i+'] <'+el.tagName+' class="'+el.className+'"> bg='+c.backgroundColor+' img='+(c.backgroundImage||'').slice(0,30)); el=el.parentElement; } }
  return out.join('\n');
});
console.log(r); await b.close();
