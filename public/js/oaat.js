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
/* Helper de traduction courante (fr/en, selon <html lang> posé par le layout) */
const L=(fr,en)=>document.documentElement.lang==='en'?en:fr;

/* 3 Menu mobile */
const bt=$('#bt'),mn=$('#mn');if(bt&&mn){bt.onclick=()=>{const o=mn.classList.toggle('grid-rows-[1fr]');mn.classList.toggle('grid-rows-[0fr]',!o);bt.setAttribute('aria-expanded',o)};$$('#mnav a').forEach(a=>a.onclick=()=>{mn.classList.replace('grid-rows-[1fr]','grid-rows-[0fr]');bt.setAttribute('aria-expanded','false')})}

/* 4 Carrousel */
const sl=$('#sl');if(sl){const s=$$(':scope>.absolute',sl),cn=$('#cn'),dt=$$('#dots button'),pv=$('#pv'),nx=$('#nx'),L=s.length;let cur=0;const go=n=>{cur=(n+L)%L;s.forEach((e,j)=>{const on=j===cur,im=$('img',e);e.inert=!on;e.classList.toggle('opacity-100',on);e.classList.toggle('z-10',on);if(im){im.classList.toggle('scale-100',on);im.classList.toggle('scale-110',!on)};$$('.t>*',e).forEach(c=>{c.classList.toggle('opacity-0',!on);c.classList.toggle('translate-y-8',!on)})});if(cn)cn.textContent=String(cur+1).padStart(2,'0')+' / 0'+L;dt.forEach((d,j)=>{const b=$('i',d);if(!b)return;b.classList.remove('animate-bar');b.style.width=j<cur?'100%':'0';if(j===cur){void b.offsetWidth;b.style.width='';b.classList.add('animate-bar')}})};dt.forEach((d,i)=>{d.onclick=()=>go(i);const b=$('i',d);if(b)b.onanimationend=()=>go(cur+1)});if(pv)pv.onclick=()=>go(cur-1);if(nx)nx.onclick=()=>go(cur+1);go(0)}

/* 5 Compteurs + revelations */
const cnt=e=>{const n=+e.dataset.n;let s=null;const f=t=>{s??=t;const p=Math.min((t-s)/1400,1);e.textContent=Math.round(n*(1-Math.pow(1-p,3))).toLocaleString(document.documentElement.lang=='en'?'en-US':'fr-FR');p<1&&requestAnimationFrame(f)};requestAnimationFrame(f)};
if(!rm){$$('[data-w]').forEach(e=>e.classList.add('[clip-path:inset(0_0_100%_0)]'));$$('[data-r]').forEach((e,i)=>{e.classList.add('opacity-0','translate-y-6','transition','ease-out');e.style.transitionDuration='900ms';e.style.transitionDelay=(i%3)*90+'ms'})}
$$('[data-n]').forEach(e=>{if(!rm)e.textContent='0'});
$$('main section:not(#accueil) h2').forEach(h=>{h.classList.add('before:block','before:h-1','before:w-0','before:bg-ochre','before:mb-4','before:transition-all','before:duration-1000');h.dataset.bar=''});
const io=new IntersectionObserver(es=>es.forEach(entry=>{if(!entry.isIntersecting)return;const t=entry.target;if(t.dataset.n!==undefined){setTimeout(()=>cnt(t),200)}else if(t.dataset.w!==undefined)t.classList.replace('[clip-path:inset(0_0_100%_0)]','[clip-path:inset(0)]');else if(t.dataset.bar!==undefined)t.classList.add('before:w-16');else{t.classList.remove('opacity-0','translate-y-6');setTimeout(()=>{t.style.transitionDelay='';t.style.transitionDuration=''},1300)};io.unobserve(t)}),{threshold:.15});
$$('[data-n],[data-w],[data-bar],[data-r]').forEach(e=>io.observe(e));

/* 6 Scroll */
const secs=$$('.nl').filter(l=>(l.getAttribute('href')||'')[0]==='#').map(l=>$(l.getAttribute('href'))).filter(Boolean),pg=$('#pg'),hd=$('#hd'),hi=$('#hi'),rg=$('#rg'),up=$('#up');const sc=()=>{const y=scrollY,h=document.documentElement.scrollHeight-innerHeight;if(pg)pg.style.width=(y/h*100)+'%';if(hd)hd.classList.toggle('shadow-lg',y>20);if(hi){hi.classList.toggle('py-3',y<=20);hi.classList.toggle('py-2',y>20)};if(rg)rg.style.strokeDashoffset=1-(y/h);if(up){up.classList.toggle('opacity-0',y<700);up.classList.toggle('pointer-events-none',y<700);up.classList.toggle('translate-y-4',y<700)};let a=0;secs.forEach((s,i)=>{if(s&&s.getBoundingClientRect().top<160)a=i});if(secs.length)$$('.nl').forEach((l,i)=>{l.classList.toggle('text-lake',i==a);l.classList.toggle('after:scale-x-100',i==a)})};addEventListener('scroll',sc,{passive:true});sc();if(up)up.onclick=()=>scrollTo({top:0,behavior:'smooth'});
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

/* 11 Sous-nav (scrollspy) — organisation (#sn .snl) & page projet (#sn a) */
const sn=$('#sn');
if(sn){
  const leSnl=$$('#sn .snl');
  const la=leSnl.length?leSnl:$$('#sn a');
  if(la.length){
    const isCn=leSnl.length>0; /* org : surlignage ochre ; projet : lake */
    const spy=()=>{
      let a=0;
      la.forEach((l,i)=>{const s=$(l.getAttribute('href'));if(s&&s.getBoundingClientRect().top<(isCn?200:220))a=i});
      la.forEach((l,i)=>{const on=i===a;l.classList.toggle(isCn?'border-ochre':'border-lake',on);l.classList.toggle('text-lake',on);l.classList.toggle('border-transparent',!on);l.classList.toggle('text-ink/70',!on)});
    };
    addEventListener('scroll',spy,{passive:true});spy();
  }
}

/* 12 Progression de la timeline */
const tl=$('#tl'),tlp=$('#tlp');
if(tl&&tlp){
  const tf=()=>{const r=tl.getBoundingClientRect();tlp.style.height=Math.max(0,Math.min(1,(innerHeight*.65-r.top)/r.height))*100+'%'};
  addEventListener('scroll',tf,{passive:true});tf();
}

/* 13 Onglets zones d'intervention */
const ztb=$('#zt');
if(ztb){
  const ztbs=$$('#zt [data-zt]'),zp=$('#zp');
  const za=b=>{const k=b.dataset.zt;ztbs.forEach(x=>{const on=x===b;x.className='rounded-full px-5 py-2.5 text-sm font-semibold transition '+(on?'bg-ochre text-deep':'border border-white/30 hover:bg-white/10');x.setAttribute('aria-selected',on)});if(!zp)return;zp.classList.add('opacity-0');setTimeout(()=>{$$('#zp [data-zp]').forEach(c=>c.classList.toggle('hidden',c.dataset.zp!==k));zp.classList.remove('opacity-0')},180)};
  ztbs.forEach(b=>b.onclick=()=>za(b));
}


/* 14 Filtres projets — toggle mobile */
const ftg=$('#ftg'),fb=$('#fb');
if(ftg&&fb){
    ftg.onclick=()=>{
        const ouvert=fb.classList.toggle('hidden');
        ftg.setAttribute('aria-expanded', !ouvert);
    };
}

/* 15 Filtrage en direct cartes projets (#q) + compteur + état vide */
const sq=$('#q'),rc=$('#rc'),em=$('#em');
if(sq){
    // data-q est slugifié (minuscules, sans accents, tirets) : on normalise
    // le terme de recherche de la même façon pour que « eau potable » matche
    // « eau-potable ».
    const slug=t=>t.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
    const maj=()=>{
        const terme=slug(sq.value);
        const cartes=$$('#gr article');
        let visibles=0;
        cartes.forEach(c=>{
            const hay=(c.dataset.q??'').toLowerCase();
             const cache=terme!=='' && !hay.includes(terme);
            c.classList.toggle('hidden',cache);
            if(!cache)visibles++;
        });
        if(rc)rc.textContent=visibles+' '+(visibles>1?'projets':'projet');
        if(em)em.classList.toggle('hidden',visibles>0);
    };
    sq.addEventListener('input',maj);
}

/* 16 Page projet — méga menu « Domaines d’intervention » (desktop) */
const mg=$('#mg'),dn=$('#nav a:nth-child(3)');
if(mg&&dn){
  let mt;const sm=v=>{mg.classList.toggle('invisible',!v);mg.classList.toggle('opacity-0',!v);mg.classList.toggle('-translate-y-2',!v)};
  [dn,mg].forEach(e=>{e.onmouseenter=()=>{clearTimeout(mt);sm(1)};e.onmouseleave=()=>mt=setTimeout(()=>sm(0),180)});
  dn.onfocus=()=>sm(1);mg.onclick=()=>sm(0);
  addEventListener('keydown',e=>e.key=='Escape'&&sm(0));
}

/* 17 Page projet — barre mobile (CTA « Soutenir ce projet ») */
const mb=$('#mb'),sol=$('#soutenir');
if(mb&&sol){addEventListener('scroll',()=>mb.classList.toggle('translate-y-full',!(scrollY>700&&sol.getBoundingClientRect().top>innerHeight*.9)),{passive:true});}

/* 18 Page projet — copier le lien */
const cpi=$('#cpi');
if(cpi){$$('.cp').forEach(b=>b.onclick=()=>{const u=location.href.split('#')[0],ok=()=>toast(L('Lien copié dans le presse-papiers.','Link copied to the clipboard.')),fb=()=>{cpi.value=u;cpi.select();try{document.execCommand('copy')}catch(e){}ok()};navigator.clipboard?navigator.clipboard.writeText(u).then(ok,fb):fb()});}

/* 19 Page projet — impression */
const pr=$('#pr');if(pr)pr.onclick=()=>print();

/* 20 Page projet — galerie (visionneuse) */
const lb=$('#lb'),li=$('#li'),GL=$$('.gl');let gi=0;
if(lb&&li&&GL.length){
  const gs=i=>{gi=(i+GL.length)%GL.length;const b=GL[gi],im=$('img',b);li.src=b.dataset.full||im.src;$('#lc').textContent=$('figcaption span',b.closest('figure')).textContent;$('#ln2').textContent=(gi+1)+' / '+GL.length};
  const gOpen=i=>{gs(i);lb.classList.replace('hidden','flex');document.documentElement.classList.add('overflow-hidden');$('#lx').focus()};
  const gClose=()=>{lb.classList.replace('flex','hidden');document.documentElement.classList.remove('overflow-hidden');GL[gi].focus()};
  GL.forEach((b,i)=>b.onclick=()=>gOpen(i));
  const lx=$('#lx');if(lx)lx.onclick=gClose;
  lb.onclick=e=>e.target==lb&&gClose();
  const lp=$('#lp'),lne=$('#ln');
  if(lp)lp.onclick=()=>gs(gi-1);
  if(lne)lne.onclick=()=>gs(gi+1);
  addEventListener('keydown',e=>{if(lb.classList.contains('flex')){if(e.key=='Escape')gClose();if(e.key=='ArrowLeft')gs(gi-1);if(e.key=='ArrowRight')gs(gi+1)}});
}

/* 21 Liens réseaux sociaux sans URL : toast (sans écraser les vrais liens du pied de page) */
addEventListener('click',e=>{const a=e.target.closest?.('a.sc');if(a&&!a.hasAttribute('onclick')&&(!a.getAttribute('href')||a.getAttribute('href')==='#')){e.preventDefault();toast(L('Lien du réseau social à renseigner.','Social link to be added.'))}});

/* 22 Liens localisés : data-href-fr/en (demande de document) + partage par e-mail (.sh) */
const apply=()=>{$$('[data-href-fr]').forEach(a=>a.href=document.documentElement.lang=='en'?a.dataset.hrefEn:a.dataset.hrefFr);$$('.sh').forEach(a=>a.href='mailto:?subject='+encodeURIComponent(document.title)+'&body='+encodeURIComponent(location.href.split('#')[0]))};
apply();

});
