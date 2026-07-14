import { chromium } from 'playwright';
const b=await chromium.launch();const ctx=await b.newContext({viewport:{width:1366,height:860}});const p=await ctx.newPage();
await p.goto('http://127.0.0.1:8000/',{waitUntil:'domcontentloaded'});
await p.evaluate(()=>localStorage.setItem('gt-theme','light'));
await p.goto('http://127.0.0.1:8000/',{waitUntil:'networkidle'}).catch(()=>{});
await p.waitForTimeout(1500);
// ensure popup visible
await p.evaluate(()=>{const o=document.querySelector('#gotripsLeadPopupOverlay');if(o){o.style.display='flex';o.classList.add('active');}});
await p.waitForTimeout(400);
await p.screenshot({path:'scratch/popup.png'});
await b.close();console.log('ok');
