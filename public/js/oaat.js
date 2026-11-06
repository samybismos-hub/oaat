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
const io=new IntersectionObserver(es=>es.forEach(entry=>{if(!entry.isIntersecting)return;const t=entry.target;if(t.dataset.n!==undefined){setTimeout(()=>cnt(t),200)}else if(t.dataset.w!==undefined)t.classList.replace('[clip-path:inset(0_0_100%_0)]','[clip-path:inset(0)]');else if(t.dataset.bar!==undefined)t.classList.add('before:w-16');else{t.classList.remove('opacity-0','translate-y-6');setTimeout(()=>{t.style.transitionDelay='';t.style.transitionDuration=''},1300)};io.unobserve(t)}),{threshold:0});
const revueleDejaVisible=t=>{if(t.dataset.w!==undefined){t.classList.replace('[clip-path:inset(0_0_100%_0)]','[clip-path:inset(0)]')}else if(t.dataset.bar!==undefined){t.classList.add('before:w-16')}else{const d=t.dataset;t.classList.remove('opacity-0','translate-y-6');setTimeout(()=>{t.style.transitionDelay='';t.style.transitionDuration=''},1300)};io.unobserve(t)};
$$('[data-n],[data-w],[data-bar],[data-r]').forEach(e=>{io.observe(e);const r=e.getBoundingClientRect();if(r.top<innerHeight&&r.bottom>0)e.dataset.n!==undefined?setTimeout(()=>cnt(e),200):revueleDejaVisible(e)});

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
if(sq && $('#gr article')){
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
  addEventListener('scroll',()=>sm(0),{passive:true});
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

/* 22 Page domaines : scrollspy de la barre #rl (domaine visible) */
const rl=$('#rl'),rla=$$('#rl a');
if(rl&&rla.length){
  const spyRl=()=>{
    let a=0;
    rla.forEach((l,i)=>{const s=$(l.getAttribute('href'));if(s&&s.getBoundingClientRect().top<250)a=i});
    rla.forEach((l,i)=>{const on=i===a;l.classList.toggle('border-ochre',on);l.classList.toggle('border-transparent',!on);l.classList.toggle('text-lake',on);l.classList.toggle('text-ink/70',!on)});
  };
  addEventListener('scroll',spyRl,{passive:true});
  spyRl();
}

/* 23 Page galerie — Médiathèque (filtres à facettes, mosaïque, sélection, visionneuse)
   Migré de Galerie.html (maquette). Le JS ne crée aucun HTML : il filtre, trie et
   positionne des <figure class="ph"> servies par PHP (attributs data-*). Ne
   s'actionne que si le catalogue (#gr + figure.ph) est présent dans la page.
   Mémoire d'implémentation : Implémentation_Album.md. */
const GR=$('#gr'),PHs=$$('figure.ph');
if(GR&&PHs.length){
  /* 23.1 Catalogue + état */
  let YMIN=1994,YMAX=2024;const _ys=PHs.map(f=>f.dataset.y?+f.dataset.y:null).filter(Number.isFinite);if(_ys.length){YMIN=Math.min(..._ys);YMAX=Math.max(..._ys)}
  const GAP=12,PAGE=24;
  const gq=$('#q'),fb2=$('#fb');
  const sw=(e,on,a,b)=>{if(a)e.classList[on?'add':'remove'](...a.split(' '));if(b)e.classList[on?'remove':'add'](...b.split(' '))};
  const nz=s=>s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');
  const refc=n=>'OAAT-'+String(n).padStart(4,'0');
  const MAILTO=document.body.dataset.mailto||'oaatrdc2000@gmail.com';
  const FI=$$('input[data-f]'),TB=$$('.tb'),CHIPS=$$('.chip');
  let vOpen=false,cur=null,VIS=[],SHOWN=[],ORDER=[];
  let H0=location.hash;
  /* Libellés de facettes lus dans la page (rendu serveur déjà localisé ; data-en optionnel) */
  const LB={};
  FI.forEach(i=>{const s=$('span.grow',i.closest('label'));(LB[i.dataset.f]??={})[i.value]=[s?s.textContent:i.value,s?s.dataset.en||s.textContent:i.value]});
  TB.forEach(b=>{const s=b.firstElementChild;(LB.cat??={})[b.dataset.c]=[s?s.textContent:b.dataset.c,s?s.dataset.en||s.textContent:b.dataset.c]});
  const lbl=(f,v)=>{const p=(LB[f]||{})[v];return p?L(p[0],p[1]):v};
  const IT=PHs.map(f=>{const d=f.dataset;return{el:f,img:$('img',f),n:+d.id,y:+(d.y||0)||null,w:+d.w,h:+d.h,r:(+d.h)/(+d.w),cat:d.cat,th:d.th,lieu:d.lieu,ori:d.ori,par:d.par?d.par.split('|'):[],col:d.col?d.col.split(' '):[],use:d.use.split(' '),q:d.q,tf:d.tf,te:d.te,pfr:d.pfr,pen:d.pen,tags:d.tags?d.tags.split('|').map(x=>x.split('~')):[],was:false,in:false}});
  ORDER=[...IT];
  const S={q:'',cat:'',th:new Set(),lieu:new Set(),par:new Set(),col:new Set(),ori:new Set(),use:new Set(),y0:YMIN,y1:YMAX,sort:'rec',view:'masonry',dens:4,limit:PAGE,sel:new Set()};
  const SETS=['th','lieu','par','col','ori','use'];
  const facetPick={th:it=>[it.th],lieu:it=>[it.lieu],par:it=>it.par,col:it=>it.col,ori:it=>[it.ori],use:it=>it.use};
  const m=(it,skip)=>{
    if(skip!='q'&&S.q&&!S.q.split(' ').every(w=>it.q.includes(w)))return false;
    if(skip!='cat'&&S.cat&&it.cat!=S.cat)return false;
    for(const g of SETS)if(skip!=g&&S[g].size&&!facetPick[g](it).some(v=>S[g].has(v)))return false;
    if(skip!='y'){if(it.y){if(it.y<S.y0||it.y>S.y1)return false}else if(S.y0!=YMIN||S.y1!=YMAX)return false}
    return true};
  const store=(k,v)=>{try{if(v===undefined)return localStorage.getItem(k);localStorage.setItem(k,v)}catch(e){}};
  /* 23.2 Tri */
  const SORTS={rec:(a,b)=>(b.y||-1)-(a.y||-1)||a.n-b.n,old:(a,b)=>(a.y||9999)-(b.y||9999)||a.n-b.n,az:(a,b)=>L(a.tf,a.te).localeCompare(L(b.tf,b.te),document.documentElement.lang)};
  const setSort=()=>{if(S.sort=='rnd'){for(let i=ORDER.length-1;i>0;i--){const j=Math.floor(Math.random()*(i+1));[ORDER[i],ORDER[j]]=[ORDER[j],ORDER[i]]}}else ORDER.sort(SORTS[S.sort]);GR.append(...ORDER.map(i=>i.el))};
  /* 23.3 Mise en page : mosaïque (colonnes les plus courtes), grille ou liste, avec transitions */
  const colsFor=()=>{const w=GR.clientWidth,mx=w<460?2:w<720?3:w<1000?4:w<1300?5:6;return Math.max(2,Math.min(S.dens,mx))};
  GR.className='group/g relative';
  PHs.forEach(f=>{f.classList.remove('mb-3','break-inside-avoid');f.classList.add('absolute','left-0','top-0','transition-[transform,width,height,opacity]','duration-500','ease-out')});
  const layout=()=>{
    const list=S.view=='list';GR.dataset.view=S.view;
    GR.classList.toggle('relative',!list);sw(GR,list,'flex flex-col gap-3');
    IT.forEach(it=>{const f=it.el;sw(f,!list,'absolute left-0 top-0 bg-mist','static flex gap-5 items-start border-b border-line bg-white py-4');if(list)sw(f,false,'absolute left-0 top-0 bg-mist')});
    if(list){GR.style.height='';IT.forEach(it=>{const s=it.el.style;s.width='';s.height='';s.transform='';s.opacity=''});return}
    const n=colsFor(),w=GR.clientWidth,cw=(w-GAP*(n-1))/n,hs=Array(n).fill(0);
    SHOWN.forEach(it=>{const k=hs.indexOf(Math.min(...hs)),h=S.view=='grid'?cw*.75:cw*it.r,s=it.el.style;
      if(!it.was){s.transition='none';s.opacity='0'}
      s.width=cw+'px';s.height=h+'px';s.transform='translate('+(k*(cw+GAP))+'px,'+hs[k]+'px)';
      if(!it.was){void it.el.offsetWidth;s.transition='';s.opacity='1'}
      hs[k]+=h+GAP});
    GR.style.height=Math.max(0,...hs)+'px'};
  let lw=0;new ResizeObserver(()=>{if(GR.clientWidth!=lw){lw=GR.clientWidth;layout()}}).observe(GR);
  /* 23.4 Interface des facettes (cases, onglets, années, chips, contrôles) */
  const updFacets=()=>{
    FI.forEach(i=>{const g=i.dataset.f,v=i.value;i.checked=S[g].has(v);const c=IT.filter(it=>m(it,g)&&facetPick[g](it).includes(v)).length,lab=i.closest('label'),cnt=$('.cnt',lab);if(cnt)cnt.textContent=c;lab.classList.toggle('opacity-40',c==0&&!i.checked)});
    TB.forEach(b=>{const c=IT.filter(it=>m(it,'cat')&&(!b.dataset.c||it.cat==b.dataset.c)).length,cnt=$('.cnt',b);if(cnt)cnt.textContent=c;sw(b,b.dataset.c==S.cat,'border-lake text-lake','border-transparent text-ink/70 hover:text-lake');b.setAttribute('aria-selected',b.dataset.c==S.cat)});
    const y0=$('#y0'),y1=$('#y1');if(y0)y0.value=S.y0;if(y1)y1.value=S.y1;const y0t=$('#y0t');if(y0t)y0t.textContent=S.y0;const y1t=$('#y1t');if(y1t)y1t.textContent=S.y1;
    const span=YMAX-YMIN||1,yr=$('#yr');if(yr){yr.style.left=((S.y0-YMIN)/span*100)+'%';yr.style.right=((YMAX-S.y1)/span*100)+'%'}
    $$('.yb').forEach(b=>{const y=+b.dataset.y,on=y>=S.y0&&y<=S.y1;b.classList.toggle('bg-lake',on);b.classList.toggle('bg-line',!on)});
    let n=0;
    CHIPS.forEach(c=>{const f=c.dataset.f,v=c.dataset.v;let on=false;
      if(f=='y'){on=S.y0!=YMIN||S.y1!=YMAX;const chy=$('#chy');if(chy&&on)chy.textContent=S.y0+' — '+S.y1}
      else if(f=='q'){on=!!S.q;const chq=$('#chq');if(chq&&on)chq.textContent='« '+((gq)&&gq.value||'').trim()+' »'}
      else if(f=='cat')on=S.cat==v;else on=S[f].has(v);
      c.classList.toggle('hidden',!on);c.classList.toggle('inline-flex',on);if(on)n++});
    const chipsEl=$('#chips');if(chipsEl){chipsEl.classList.toggle('hidden',!n);chipsEl.classList.toggle('flex',!!n)}
    const fn=$('#fn');if(fn){fn.textContent=n;fn.classList.toggle('hidden',!n)}};
  const updCtl=()=>{const so2=$('#so');if(so2)so2.value=S.sort;const dens2=$('#dens');if(dens2)dens2.value=S.dens;$$('.vb').forEach(b=>sw(b,b.dataset.v==S.view,'bg-mist text-lake','text-ink/60'))};
  /* 23.5 Sélection (persistée en localStorage sous 'oaat-sel') */
  try{JSON.parse(store('oaat-sel')||'[]').forEach(n=>S.sel.add(n))}catch(e){}
  const mailtoSel=()=>{const ls=[...S.sel].sort((a,b)=>a-b).map(n=>{const it=IT.find(i=>i.n==n);return refc(n)+' — '+L(it.tf,it.te)});return 'mailto:'+MAILTO+'?subject='+encodeURIComponent(L('Demande d’images (','Image request (')+S.sel.size+')')+'&body='+encodeURIComponent(L('Bonjour,\n\nNous souhaitons obtenir les images suivantes :\n\n','Hello,\n\nWe would like to obtain the following images:\n\n')+ls.join('\n')+L('\n\nUsage prévu :\nMédia / organisation :\n\nMerci d’avance,','\n\nIntended use:\nMedia / organisation:\n\nThank you,'))};
  const updSel=()=>{IT.forEach(it=>{const on=S.sel.has(it.n),sEl=$('.sel',it.el);if(sEl)sEl.setAttribute('aria-pressed',on);sw(it.el,on,'ring-2 ring-inset ring-lake')});const n=S.sel.size,tn=$('#tn'),tray=$('#tray'),trayList=$('#tl');if(tn)tn.textContent=n;if(tray)tray.classList.toggle('translate-y-full',!n);if(trayList)trayList.textContent=[...S.sel].sort((a,b)=>a-b).map(refc).join(', ');const treq=$('#treq');if(treq)treq.href=mailtoSel();if(vOpen)updV()};
  const togSel=n=>{S.sel.has(n)?S.sel.delete(n):S.sel.add(n);store('oaat-sel',JSON.stringify([...S.sel]));updSel()};
  /* 23.6 Rafraîchissement global + URL partageable (history.replaceState) */
  const syncUrl=()=>{try{const p=new URLSearchParams();if(S.q)p.set('q',gq.value.trim());if(S.cat)p.set('c',S.cat);SETS.forEach(g=>{if(S[g].size)p.set(g,[...S[g]].join(','))});if(S.y0!=YMIN||S.y1!=YMAX)p.set('y',S.y0+'-'+S.y1);if(S.sort!='rec')p.set('s',S.sort);if(S.view!='masonry')p.set('v',S.view);const q=p.toString();history.replaceState(null,'',location.pathname+(q?'?'+q:'')+(vOpen&&cur?'#g'+cur.n:''))}catch(e){}};
  const refresh=()=>{
    VIS=ORDER.filter(it=>m(it));SHOWN=VIS.slice(0,S.limit);
    IT.forEach(it=>{it.in=SHOWN.includes(it);it.el.classList.toggle('hidden',!it.in)});
    updFacets();layout();IT.forEach(it=>it.was=it.in);
    const em=$('#em');if(em)em.classList.toggle('hidden',VIS.length>0);
    const rcEl=$('#rc');if(rcEl)rcEl.textContent=VIS.length+' '+L('médias','media');
    const more=VIS.length-SHOWN.length,mb=$('#more');if(mb){mb.classList.toggle('hidden',more<=0);mb.textContent=L('Afficher plus','Show more')+' ('+more+')'}
    const pg2=$('#pg2');if(pg2)pg2.textContent=SHOWN.length+' / '+VIS.length+' '+L('médias affichés','media shown');
    updSel();updCtl();syncUrl()};
  const clearAll=()=>{S.q='';if(gq)gq.value='';S.cat='';SETS.forEach(g=>S[g].clear());S.y0=YMIN;S.y1=YMAX;S.limit=PAGE;refresh()};
  /* 23.7 Commandes */
  if(fb2)fb2.addEventListener('change',e=>{const i=e.target;if(i.dataset&&i.dataset.f){S[i.dataset.f][i.checked?'add':'delete'](i.value);S.limit=PAGE;refresh()}});
  ['y0','y1'].forEach(id=>{const el=$('#'+id);if(el)el.addEventListener('input',e=>{let a=+$('#y0').value,b=+$('#y1').value;if(a>b){if(e.target.id=='y0')a=b;else b=a}S.y0=a;S.y1=b;S.limit=PAGE;refresh()})});
  $$('.dec').forEach(b=>b.onclick=()=>{S.y0=Math.max(YMIN,+b.dataset.y0);S.y1=Math.min(YMAX,+b.dataset.y1);S.limit=PAGE;refresh()});
  const tbs=$('#tabs');if(tbs)tbs.onclick=e=>{const b=e.target.closest('.tb');if(b){S.cat=b.dataset.c;S.limit=PAGE;refresh()}};
  if(gq)gq.oninput=e=>{S.q=nz(e.target.value.trim());S.limit=PAGE;refresh()};
  const toGrid=()=>{const t=$('#tabs');if(t)t.scrollIntoView({behavior:rm?'auto':'smooth',block:'start'})};
  $$('[data-qs]').forEach(b=>b.onclick=()=>{if(gq)gq.value=b.dataset.qs;S.q=nz(b.dataset.qs);S.limit=PAGE;refresh();toGrid()});
  $$('.cc').forEach(b=>b.onclick=()=>{const c=b.dataset.col,had=S.col.size==1&&S.col.has(c);S.col.clear();if(!had)S.col.add(c);S.limit=PAGE;refresh();toGrid()});
  const chips=$('#chips');if(chips)chips.onclick=e=>{const c=e.target.closest('.chip,.chip-clear');if(!c)return;if(c.id=='cclr'){clearAll();return}const f=c.dataset.f,v=c.dataset.v;if(f=='cat')S.cat='';else if(f=='y'){S.y0=YMIN;S.y1=YMAX}else if(f=='q'){S.q='';if(gq)gq.value=''}else S[f].delete(v);refresh()};
  const fclr=$('#fclr');if(fclr)fclr.onclick=clearAll;$$('.emc').forEach(b=>b.onclick=clearAll);
  const soEl=$('#so');if(soEl)soEl.onchange=e=>{S.sort=e.target.value;setSort();refresh()};
  $$('.vb').forEach(b=>b.onclick=()=>{S.view=b.dataset.v;store('oaat-view',S.view);refresh()});
  const densEl=$('#dens');if(densEl)densEl.oninput=e=>{S.dens=+e.target.value;store('oaat-dens',S.dens);layout()};
  const moreEl=$('#more');if(moreEl)moreEl.onclick=()=>{S.limit+=PAGE;refresh()};
  const sntEl=$('#snt');if(sntEl)new IntersectionObserver(es=>{if(es[0].isIntersecting&&VIS.length>SHOWN.length){S.limit+=PAGE;refresh()}},{rootMargin:'500px'}).observe(sntEl);
  const fpb=$('#fb'),fbk=$('#fbk');
  const fDrawer=on=>{if(!fpb)return;fpb.classList.toggle('-translate-x-full',!on);if(fbk)fbk.classList.toggle('hidden',!on);document.documentElement.classList.toggle('overflow-hidden',on&&innerWidth<1024)};
  const fbtn=$('#fbtn');if(fbtn)fbtn.onclick=()=>fDrawer(true);const fclose=$('#fclose');if(fclose)fclose.onclick=()=>fDrawer(false);if(fbk)fbk.onclick=()=>fDrawer(false);
  GR.addEventListener('click',e=>{const s=e.target.closest('.sel');if(s){e.stopPropagation();togSel(+s.closest('figure').dataset.id);return}const o=e.target.closest('.open');if(o)vOpenAt(IT.find(i=>i.el==o.closest('figure')),o)});
  const tall=$('#tall');if(tall)tall.onclick=()=>{VIS.forEach(it=>S.sel.add(it.n));store('oaat-sel',JSON.stringify([...S.sel]));updSel()};
  const tinv=$('#tinv');if(tinv)tinv.onclick=()=>{VIS.forEach(it=>S.sel.has(it.n)?S.sel.delete(it.n):S.sel.add(it.n));store('oaat-sel',JSON.stringify([...S.sel]));updSel()};
  const tclr=$('#tclr');if(tclr)tclr.onclick=()=>{S.sel.clear();store('oaat-sel','[]');updSel()};
  const copyText=(u,msg)=>{const i=$('#cpi');if(!i)return;const ok=()=>toast(msg),fallback=()=>{i.value=u;i.select();try{document.execCommand('copy')}catch(e){}ok()};if(navigator.clipboard)navigator.clipboard.writeText(u).then(ok,fallback);else fallback()};
  const tcp=$('#tcp');if(tcp)tcp.onclick=()=>copyText([...S.sel].sort((a,b)=>a-b).map(n=>refc(n)+' — '+L(IT.find(i=>i.n==n).tf,IT.find(i=>i.n==n).te)).join('\n'),L('Références copiées.','References copied.'));
  /* 23.8 Aide clavier */
  const help=$('#help'),showHelp=()=>{if(help)help.classList.replace('hidden','flex')},hideHelp=()=>{if(help)help.classList.replace('flex','hidden')};
  const hlp=$('#hlp');if(hlp)hlp.onclick=showHelp;const hx=$('#hx');if(hx)hx.onclick=hideHelp;if(help)help.onclick=e=>{if(e.target==help)hideHelp()};
  /* 23.9 Visionneuse (#vw) : zoom, navigation, diaporama, plein écran, partage, similarité */
  const VW=$('#vw'),VI=$('#vi'),VST=$('#vst');let zs=1,tx=0,ty=0,play=false,tmo=0,idle=0,prevFocus=null;
  const applyZ=()=>{VI.style.transform='translate('+tx+'px,'+ty+'px) scale('+zs+')';VI.classList.toggle('cursor-grab',zs>1)};
  const resetZ=()=>{zs=1;tx=ty=0;applyZ()};
  const zoomAt=(f,cx,cy)=>{const r=VST.getBoundingClientRect(),px=cx-r.left-r.width/2,py=cy-r.top-r.height/2,ns=Math.max(1,Math.min(6,zs*f));if(ns==1){tx=ty=0}else{tx=px-(px-tx)*ns/zs;ty=py-(py-ty)*ns/zs}zs=ns;applyZ()};
  const zoomCenter=f=>{const r=VST.getBoundingClientRect();zoomAt(f,r.left+r.width/2,r.top+r.height/2)};
  const idx=()=>VIS.indexOf(cur);
  const reqLink=it=>'mailto:'+MAILTO+'?subject='+encodeURIComponent(L('Demande d’autorisation — ','Permission request — ')+refc(it.n))+'&body='+encodeURIComponent(L('Bonjour,\n\nNous souhaitons utiliser l’image '+refc(it.n)+' (« '+it.tf+' »).\n\nUsage prévu :\nMédia / organisation :\n\nMerci d’avance,','Hello,\n\nWe would like to use image '+refc(it.n)+' (“'+it.te+'”).\n\nIntended use:\nMedia / organisation:\n\nThank you,'));
  const updV=()=>{if(!cur)return;const it=cur;
    const vt=$('#vt');if(vt)vt.textContent=L(it.tf,it.te);const vcat=$('#vcat');if(vcat)vcat.textContent=lbl('cat',it.cat);
    const vm1=$('#vm1');if(vm1)vm1.textContent=refc(it.n);const vm2=$('#vm2');if(vm2)vm2.textContent=it.y||'—';const vm3=$('#vm3');if(vm3)vm3.textContent=it.th?lbl('th',it.th):'—';
    const vm4=$('#vm4');if(vm4)vm4.textContent=L(it.pfr,it.pen)||lbl('lieu',it.lieu);const vm5=$('#vm5');if(vm5)vm5.textContent=it.par.length?it.par.map(p=>lbl('par',p)).join(', '):'—';const vm6=$('#vm6');if(vm6)vm6.textContent=it.col.length?it.col.map(c=>lbl('col',c)).join(', '):'—';const vm7=$('#vm7');if(vm7)vm7.textContent=lbl('ori',it.ori);const vm8=$('#vm8');if(vm8)vm8.textContent=it.w+' × '+it.h+' px';
    const vm9=$('#vm9');if(vm9)vm9.textContent=it.use.map(u=>lbl('use',u)).join(', ');const vm10=$('#vm10');if(vm10)vm10.textContent='© OAAT';
    $$('.vtg').forEach(b=>b.classList.toggle('hidden',!it.tags.some(t=>t[0]==b.dataset.tg)));
    const on=S.sel.has(it.n),vsEl=$('#vs');if(vsEl){vsEl.setAttribute('aria-pressed',on);const vo=$('.v-off',vsEl);if(vo)vo.classList.toggle('hidden',on);const vn2=$('.v-on',vsEl);if(vn2)vn2.classList.toggle('hidden',!on)}
    const vcn=$('#vcn');if(vcn)vcn.textContent=(idx()+1)+' / '+VIS.length;
    $$('.fs').forEach(b=>{const n=+b.dataset.id,act=n==it.n;b.classList.toggle('hidden',!VIS.some(i=>i.n==n));sw(b,act,'opacity-100 outline outline-2 outline-ochre','opacity-60')});
    const a=$('.fs[data-id="'+it.n+'"]');if(a)a.scrollIntoView({inline:'center',block:'nearest'});
    const d=$('#vd');if(d){d.href=it.img.src;d.download=it.el.dataset.dl||refc(it.n)+'.svg'}const vsrc=$('#vsrc');if(vsrc)vsrc.href=it.el.dataset.src;const vsrt=$('#vsrt');if(vsrt)vsrt.textContent=L(it.el.dataset.sfr,it.el.dataset.sen);const vq=$('#vq');if(vq)vq.href=reqLink(it)};
  const showV=(it,dir)=>{cur=it;if(VI.dataset.n!=it.n){VI.src=it.img.src;VI.alt=it.tf;VI.dataset.n=it.n;resetZ();if(dir&&!rm)VI.animate([{opacity:.25,transform:'translateX('+dir*60+'px)'},{opacity:1,transform:'none'}],{duration:260,easing:'ease-out'})}
    [VIS[(idx()+1)%VIS.length],VIS[(idx()-1+VIS.length)%VIS.length]].forEach(n=>{if(n)new Image().src=n.img.src});updV();syncUrl()};
  const setPlay=on=>{play=on;clearTimeout(tmo);const pr=$('#vpr');if(pr){pr.style.transition='none';pr.style.width='0';if(on){void pr.offsetWidth;pr.style.transition='width 4s linear';pr.style.width='100%';tmo=setTimeout(()=>{vGo(1,true);setPlay(true)},4000)}}const playBtn=$('#vsl');if(playBtn)sw(playBtn,on,'bg-ochre text-ink border-ochre');const vbar=$('#vbar');if(!on&&vbar)vbar.classList.remove('opacity-0')};
  const vGo=(d,keep)=>{if(!VIS.length)return;if(!keep)setPlay(false);const i=idx();showV(VIS[((i<0?0:i)+d+VIS.length)%VIS.length],d)};
  const vOpenAt=(it,from)=>{if(!it)return;prevFocus=document.activeElement;vOpen=true;if(VW)VW.classList.remove('hidden');document.documentElement.classList.add('overflow-hidden');
    showV(it,0);
    if(from&&!rm){const a=from.getBoundingClientRect(),b=VST.getBoundingClientRect(),s=Math.min(1,a.width/b.width);VI.animate([{transform:'translate('+(a.left+a.width/2-b.left-b.width/2)+'px,'+(a.top+a.height/2-b.top-b.height/2)+'px) scale('+s+')',opacity:.4},{transform:'none',opacity:1}],{duration:380,easing:'cubic-bezier(.2,.8,.2,1)'})}
    const vxEl=$('#vx');if(vxEl)vxEl.focus()};
  const vClose=()=>{setPlay(false);vOpen=false;cur=null;VI.dataset.n='';if(VW)VW.classList.add('hidden');document.documentElement.classList.remove('overflow-hidden');if(document.fullscreenElement)document.exitFullscreen();syncUrl();if(prevFocus&&prevFocus.focus)prevFocus.focus()};
  const vxEl=$('#vx');if(vxEl)vxEl.onclick=vClose;const vp=$('#vp');if(vp)vp.onclick=()=>vGo(-1);const vnEl=$('#vn');if(vnEl)vnEl.onclick=()=>vGo(1);
  const vzi=$('#vzi');if(vzi)vzi.onclick=()=>zoomCenter(1.4);const vzo=$('#vzo');if(vzo)vzo.onclick=()=>zoomCenter(1/1.4);const vz0=$('#vz0');if(vz0)vz0.onclick=resetZ;
  const vsl=$('#vsl');if(vsl)vsl.onclick=()=>setPlay(!play);const sl0=$('#sl0');if(sl0)sl0.onclick=()=>{if(!VIS.length)return;vOpenAt(VIS[0]);setPlay(true)};
  const vfs=$('#vfs');if(vfs)vfs.onclick=()=>document.fullscreenElement?document.exitFullscreen():(VW.requestFullscreen&&VW.requestFullscreen());
  const vii=$('#vii');if(vii)vii.onclick=()=>{const vin=$('#vin');if(vin)vin.classList.toggle('hidden')};
  const vsBtn=$('#vs');if(vsBtn)vsBtn.onclick=()=>cur&&togSel(cur.n);
  const vc=$('#vc');if(vc)vc.onclick=()=>copyText(location.href,L('Lien copié dans le presse-papiers.','Link copied to the clipboard.'));
  const vsh=$('#vsh');if(vsh)vsh.onclick=()=>{const u=location.href,tt=cur?L(cur.tf,cur.te):document.title;navigator.share?navigator.share({title:tt,url:u}).catch(()=>{}):copyText(u,L('Lien copié dans le presse-papiers.','Link copied to the clipboard.'))};
  const vsim=$('#vsim');if(vsim)vsim.onclick=()=>{if(!cur)return;const it=cur;vClose();clearAll();if(it.th)S.th.add(it.th);else S.cat=it.cat;refresh();toGrid();toast(L('Images similaires','Similar images'))};
  $$('.vtg').forEach(b=>b.onclick=()=>{vClose();clearAll();if(gq)gq.value=b.dataset.tg;S.q=nz(b.dataset.tg);refresh();toGrid()});
  $$('.fs').forEach(b=>b.onclick=()=>{setPlay(false);showV(IT.find(i=>i.n==+b.dataset.id),0)});
  if(VST){
    VST.addEventListener('wheel',e=>{e.preventDefault();zoomAt(e.deltaY<0?1.18:1/1.18,e.clientX,e.clientY)},{passive:false});
    const PT=new Map();let sx=0,sy=0,stx=0,sty=0,pd=1,pz=1;
    VST.addEventListener('pointerdown',e=>{if(e.target.closest('button'))return;try{VST.setPointerCapture(e.pointerId)}catch(x){}PT.set(e.pointerId,[e.clientX,e.clientY]);sx=e.clientX;sy=e.clientY;stx=tx;sty=ty;if(PT.size==2){const[a,b]=[...PT.values()];pd=Math.hypot(a[0]-b[0],a[1]-b[1])||1;pz=zs}});
    VST.addEventListener('pointermove',e=>{if(!PT.has(e.pointerId))return;PT.set(e.pointerId,[e.clientX,e.clientY]);if(PT.size==2){const[a,b]=[...PT.values()];zs=Math.max(1,Math.min(6,pz*Math.hypot(a[0]-b[0],a[1]-b[1])/pd));if(zs==1){tx=ty=0}applyZ();return}if(zs>1){tx=stx+e.clientX-sx;ty=sty+e.clientY-sy;applyZ()}});
    VST.addEventListener('pointerup',e=>{if(!PT.has(e.pointerId))return;PT.delete(e.pointerId);const dx=e.clientX-sx,dy=e.clientY-sy;if(zs==1&&PT.size==0&&Math.abs(dx)>60&&Math.abs(dy)<90)vGo(dx<0?1:-1)});
    VST.addEventListener('pointercancel',e=>PT.delete(e.pointerId));
    VST.addEventListener('dblclick',e=>zs>1?zoomAt(1e-6,e.clientX,e.clientY):zoomAt(2.5,e.clientX,e.clientY));
  }
  if(VW)VW.addEventListener('pointermove',()=>{if(!play)return;const vbar=$('#vbar');if(vbar){vbar.classList.remove('opacity-0');clearTimeout(idle);idle=setTimeout(()=>{if(play)vbar.classList.add('opacity-0')},2500)}});
  /* 23.10 Clavier (raccourcis page + visionneuse + piège Tab) */
  addEventListener('keydown',e=>{
    if(e.ctrlKey||e.metaKey||e.altKey)return;
    if(help&&help.classList.contains('flex')){if(e.key=='Escape')hideHelp();return}
    const tg=e.target.tagName,typing=tg=='INPUT'&&e.target.type!='range'||tg=='TEXTAREA'||tg=='SELECT';
    if(vOpen){
      if(e.key=='Tab'&&VW){const f=[...VW.querySelectorAll('button,a[href]')].filter(x=>x.offsetParent!==null&&!x.disabled);if(f.length){const i=f.indexOf(document.activeElement);const nx=e.shiftKey?(i<=0?f.length-1:i-1):(i==f.length-1?0:i+1);e.preventDefault();f[nx].focus()}return}
      const k=e.key;
      if(k=='Escape')vClose();else if(k=='ArrowLeft')vGo(-1);else if(k=='ArrowRight')vGo(1);
      else if(k=='+'||k=='=')zoomCenter(1.4);else if(k=='-')zoomCenter(1/1.4);else if(k=='0')resetZ();
      else if(k==' '){e.preventDefault();setPlay(!play)}else if(k=='i'||k=='I')vii&&vii.click();
      else if(k=='s'||k=='S'){if(cur)togSel(cur.n)}else if(k=='c'||k=='C')vc&&vc.click();else if(k=='f'||k=='F')vfs&&vfs.click();
      return}
    if(typing){if(e.key=='Escape')e.target.blur();return}
    if(e.key=='/'){e.preventDefault();gq&&gq.focus()}
    else if(e.key=='?')showHelp();
    else if(e.key=='g'||e.key=='G'){const o=['masonry','grid','list'];S.view=o[(o.indexOf(S.view)+1)%3];store('oaat-view',S.view);refresh()}
    else if(e.key=='f'||e.key=='F')fDrawer(true);
    else if(e.key=='Escape')fDrawer(false)});
  /* 23.11 Initialisation : préférences stockées, paramètres d'URL, lien direct #gN */
  (()=>{const p=new URLSearchParams(location.search),v=store('oaat-view'),dd=+store('oaat-dens');if(v)S.view=v;if(dd>=2&&dd<=6)S.dens=dd;
    S.q=nz(p.get('q')||'');if(gq)gq.value=p.get('q')||'';S.cat=p.get('c')||'';SETS.forEach(g=>(p.get(g)||'').split(',').filter(Boolean).forEach(x=>S[g].add(x)));
    const y=(p.get('y')||'').split('-').map(Number);if(y.length==2&&y[0]&&y[1]){S.y0=Math.max(YMIN,y[0]);S.y1=Math.min(YMAX,y[1])}
    if(p.get('s')&&(p.get('s')in SORTS||p.get('s')=='rnd'))S.sort=p.get('s');if(p.get('v'))S.view=p.get('v');
    if(!['masonry','grid','list'].includes(S.view))S.view='masonry';setSort()})();
  refresh();
  const hh=()=>{const h=H0||location.hash;H0='';const mm=h.match(/^#g(\d+)$/);if(!mm)return;const it=IT.find(i=>i.n==+mm[1]);if(!it)return;if(!VIS.includes(it))clearAll();vOpenAt(it)};
  hh();addEventListener('hashchange',hh);
}
});
