/* oaat.js */
'use strict';
const $=(s,e)=>e?e.querySelector(s):document.querySelector(s);
const $$=(s,e)=>[...(e||document).querySelectorAll(s)];
const rm=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
addEventListener('DOMContentLoaded',()=>{

/* 1 Placeholders */
const ph=(l,h)=>'data:image/svg+xml;utf8,'+encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 500"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="hsl('+h+',45%,26%)"/><stop offset="1" stop-color="hsl('+(+h+40)+',50%,44%)"/></linearGradient></defs><rect width="800" height="500" fill="url(#g)"/><path d="M0 380c120-60 200 40 340-10s220-80 460 0v130H0z" fill="#fff" opacity=".1"/><path d="M0 430c150-50 260 30 400-10s250-40 400 10v70H0z" fill="#fff" opacity=".12"/><text x="400" y="245" fill="#fff" fill-opacity=".85" font-family="sans-serif" font-size="24" text-anchor="middle">'+l+'</text><text x="400" y="278" fill="#fff" fill-opacity=".6" font-family="sans-serif" font-size="15" text-anchor="middle">image fictive</text></svg>');
$$('img[data-ph]').forEach(i=>{if(!i.getAttribute('src')){const[a,b]=i.dataset.ph.split('|');i.src=ph(a,b)}});

/* 2 Toast */
const toast=m=>{const t=$('#ts');if(!t)return;t.textContent=m;t.classList.remove('translate-y-20','opacity-0');clearTimeout(t._t);t._t=setTimeout(()=>t.classList.add('translate-y-20','opacity-0'),2800)};
addEventListener('toast',e=>toast(e.detail||''));

/* 3 Menu mobile */
const bt=$('#bt'),mn=$('#mn');if(bt&&mn){bt.onclick=()=>{const o=mn.classList.toggle('grid-rows-[1fr]');mn.classList.toggle('grid-rows-[0fr]',!o);bt.setAttribute('aria-expanded',o)};$$('#mnav a').forEach(a=>a.onclick=()=>{mn.classList.replace('grid-rows-[1fr]','grid-rows-[0fr]');bt.setAttribute('aria-expanded','false')})}

/* 4 Carrousel */
const sl=$('#sl');if(sl){const s=$$(':scope>.absolute',sl),cn=$('#cn'),dt=$$('#dots button'),pv=$('#pv'),nx=$('#nx'),L=s.length;let cur=0;const go=n=>{cur=(n+L)%L;s.forEach((e,j)=>{const on=j===cur,im=$('img',e);e.inert=!on;e.classList.toggle('opacity-100',on);e.classList.toggle('z-10',on);if(im){im.classList.toggle('scale-100',on);im.classList.toggle('scale-110',!on)};$$('.t>*',e).forEach(c=>{c.classList.toggle('opacity-0',!on);c.classList.toggle('translate-y-8',!on)})});if(cn)cn.textContent=String(cur+1).padStart(2,'0')+' / 0'+L;dt.forEach((d,j)=>{const b=$('i',d);if(!b)return;b.classList.remove('animate-bar');b.style.width=j<cur?'100%':'0';if(j===cur){void b.offsetWidth;b.style.width='';b.classList.add('animate-bar')}})};dt.forEach((d,i)=>{d.onclick=()=>go(i);const b=$('i',d);if(b)b.onanimationend=()=>go(cur+1)});if(pv)pv.onclick=()=>go(cur-1);if(nx)nx.onclick=()=>go(cur+1);go(0)}

/* 5 Compteurs + revelations */
const cnt=e=>{const n=+e.dataset.n;let s=null;const f=t=>{s??=t;const p=Math.min((t-s)/1400,1);e.textContent=Math.round(n*(1-Math.pow(1-p,3))).toLocaleString(document.documentElement.lang=='en'?'en-US':'fr-FR');p<1&&requestAnimationFrame(f)};requestAnimationFrame(f)};
if(!rm){$$('[data-w]').forEach(e=>e.classList.add('[clip-path:inset(0_0_100%_0)]'));$$('[data-r]').forEach((e,i)=>{e.classList.add('opacity-0','translate-y-6','transition','ease-out');e.style.transitionDuration='900ms';e.style.transitionDelay=(i%3)*90+'ms'})}
$$('main section:not(#accueil) h2').forEach(h=>{h.classList.add('before:block','before:h-1','before:w-0','before:bg-ochre','before:mb-4','before:transition-all','before:duration-1000');h.dataset.bar=''});
const io=new IntersectionObserver(es=>es.forEach(entry=>{if(!entry.isIntersecting)return;const t=entry.target;if(t.dataset.n!==undefined)cnt(t);else if(t.dataset.w!==undefined)t.classList.replace('[clip-path:inset(0_0_100%_0)]','[clip-path:inset(0)]');else if(t.dataset.bar!==undefined)t.classList.add('before:w-16');else{t.classList.remove('opacity-0','translate-y-6');setTimeout(()=>{t.style.transitionDelay='';t.style.transitionDuration=''},1300)};io.unobserve(t)}),{threshold:.15});
$$('[data-n],[data-w],[data-bar],[data-r]').forEach(e=>io.observe(e));

/* 6 Scroll */
const secs=$$('.nl').map(l=>$(l.getAttribute('href'))).filter(Boolean);const pg=$('#pg'),hd=$('#hd'),hi=$('#hi'),rg=$('#rg'),up=$('#up');const sc=()=>{const y=scrollY,h=document.documentElement.scrollHeight-innerHeight;if(pg)pg.style.width=(y/h*100)+'%';if(hd)hd.classList.toggle('shadow-lg',y>20);if(hi){hi.classList.toggle('py-3',y<=20);hi.classList.toggle('py-2',y>20)};if(rg)rg.style.strokeDashoffset=1-(y/h);if(up){up.classList.toggle('opacity-0',y<700);up.classList.toggle('pointer-events-none',y<700);up.classList.toggle('translate-y-4',y<700)};let a=0;secs.forEach((s,i)=>{if(s&&s.getBoundingClientRect().top<160)a=i});$$('.nl').forEach((l,i)=>{l.classList.toggle('text-lake',i==a);l.classList.toggle('after:scale-x-100',i==a)})};addEventListener('scroll',sc,{passive:true});sc();if(up)up.onclick=()=>scrollTo({top:0,behavior:'smooth'});
/* 7 Tabs timeline */
$$('#tabs [data-tab]').forEach(btn=>btn.onclick=()=>{const i=btn.dataset.tab;$$('#tabs [data-tab]').forEach(b=>{const a=b===btn;b.setAttribute('aria-selected',a);b.className='rounded-full px-5 py-2 text-sm font-semibold transition '+(a?'bg-lake text-white shadow':'bg-white text-ink/70 hover:bg-white hover:text-lake ring-1 ring-line')});const p=$('#tp [data-panel="'+i+'"]');if(p){$$('#tp .tl-panel').forEach(x=>x.classList.add('hidden'));p.classList.remove('hidden')}})

/* 8 Pick was */
const pick=v=>{const s=$('#cf select[name="subject"]');if(s)s.value=v;$('#cf')?.scrollIntoView({behavior:'smooth'});$$('.pw').forEach(b=>{b.classList.toggle('border-lake',b.dataset.v==v);b.classList.toggle('bg-mist',b.dataset.v==v)});setTimeout(()=>$('#cf textarea')?.focus({preventScroll:true}),700)};
$$('.pw,[data-pick]').forEach(b=>b.onclick=e=>{e.preventDefault();pick(b.dataset.v||b.dataset.pick)});

/* 9 Validation contact */
const cf=$('#cf');if(cf)cf.onsubmit=e=>{const f=e.target,v=[f.name.value.trim().length>1,/^\S+@\S+\.\S+$/.test(f.email.value),f.message.value.trim().length>=10];const ok=v.every(Boolean);$$('.er',f).forEach((p,i)=>{p.classList.toggle('hidden',v[i]);p.previousElementSibling.classList.toggle('border-red-500',!v[i])});if(!ok){e.preventDefault();toast(document.documentElement.lang=='en'?'Please check the highlighted fields.':'Vérifiez les champs signalés.')}};

/* 9b Validation besoin */
const bf=$('#bf');if(bf)bf.onsubmit=e=>{const f=e.target,v=[f.contact_name.value.trim().length>1,/^\S+@\S+\.\S+$/.test(f.email.value),f.description.value.trim().length>=10];const ok=v.every(Boolean);if(!ok){e.preventDefault();toast(document.documentElement.lang=='en'?'Please check the highlighted fields.':'Vérifiez les champs signalés.')}};

/* 10 Spotlight */
addEventListener('pointermove',e=>{const c=e.target.closest?.('.sp');if(c){const r=c.getBoundingClientRect();c.style.setProperty('--x',e.clientX-r.left+'px');c.style.setProperty('--y',e.clientY-r.top+'px')}});

});
