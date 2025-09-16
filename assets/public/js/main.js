// -------------------- Flash fade + countries select --------------------
document.addEventListener("DOMContentLoaded", function () {
  // flash fade-out
  ["flash-message","flash-notify"].forEach(id=>{
    const el=document.getElementById(id);
    if(el){
      setTimeout(()=>{ el.style.transition="opacity .5s"; el.style.opacity="0";
        setTimeout(()=>el.remove(),500);
      },3000);
    }
  });

  // countries list
  const countries = [
    { name:"Afghanistan", dial:"+93", flag:"🇦🇫" },
    { name:"Albania", dial:"+355", flag:"🇦🇱" },
    { name:"Algeria", dial:"+213", flag:"🇩🇿" },
    { name:"Andorra", dial:"+376", flag:"🇦🇩" },
    { name:"Angola", dial:"+244", flag:"🇦🇴" },
    { name:"Argentina", dial:"+54", flag:"🇦🇷" },
    { name:"Armenia", dial:"+374", flag:"🇦🇲" },
    { name:"Australia", dial:"+61", flag:"🇦🇺" },
    { name:"Austria", dial:"+43", flag:"🇦🇹" },
    { name:"Azerbaijan", dial:"+994", flag:"🇦🇿" },
    { name:"Bahrain", dial:"+973", flag:"🇧🇭" },
    { name:"Bangladesh", dial:"+880", flag:"🇧🇩" },
    { name:"Belarus", dial:"+375", flag:"🇧🇾" },
    { name:"Belgium", dial:"+32", flag:"🇧🇪" },
    { name:"Bosnia & Herzegovina", dial:"+387", flag:"🇧🇦" },
    { name:"Brazil", dial:"+55", flag:"🇧🇷" },
    { name:"Bulgaria", dial:"+359", flag:"🇧🇬" },
    { name:"Canada", dial:"+1", flag:"🇨🇦" },
    { name:"China", dial:"+86", flag:"🇨🇳" },
    { name:"Croatia", dial:"+385", flag:"🇭🇷" },
    { name:"Cyprus", dial:"+357", flag:"🇨🇾" },
    { name:"Czechia", dial:"+420", flag:"🇨🇿" },
    { name:"Denmark", dial:"+45", flag:"🇩🇰" },
    { name:"Egypt", dial:"+20", flag:"🇪🇬" },
    { name:"Estonia", dial:"+372", flag:"🇪🇪" },
    { name:"Finland", dial:"+358", flag:"🇫🇮" },
    { name:"France", dial:"+33", flag:"🇫🇷" },
    { name:"Georgia", dial:"+995", flag:"🇬🇪" },
    { name:"Germany", dial:"+49", flag:"🇩🇪" },
    { name:"Greece", dial:"+30", flag:"🇬🇷" },
    { name:"Hungary", dial:"+36", flag:"🇭🇺" },
    { name:"Iceland", dial:"+354", flag:"🇮🇸" },
    { name:"India", dial:"+91", flag:"🇮🇳" },
    { name:"Indonesia", dial:"+62", flag:"🇮🇩" },
    { name:"Iran", dial:"+98", flag:"🇮🇷" },
    { name:"Iraq", dial:"+964", flag:"🇮🇶" },
    { name:"Ireland", dial:"+353", flag:"🇮🇪" },
    { name:"Israel", dial:"+972", flag:"🇮🇱" },
    { name:"Italy", dial:"+39", flag:"🇮🇹" },
    { name:"Japan", dial:"+81", flag:"🇯🇵" },
    { name:"Jordan", dial:"+962", flag:"🇯🇴" },
    { name:"Kazakhstan", dial:"+7", flag:"🇰🇿" },
    { name:"Kuwait", dial:"+965", flag:"🇰🇼" },
    { name:"Kyrgyzstan", dial:"+996", flag:"🇰🇬" },
    { name:"Lebanon", dial:"+961", flag:"🇱🇧" },
    { name:"Libya", dial:"+218", flag:"🇱🇾" },
    { name:"Lithuania", dial:"+370", flag:"🇱🇹" },
    { name:"Luxembourg", dial:"+352", flag:"🇱🇺" },
    { name:"Malaysia", dial:"+60", flag:"🇲🇾" },
    { name:"Malta", dial:"+356", flag:"🇲🇹" },
    { name:"Mexico", dial:"+52", flag:"🇲🇽" },
    { name:"Moldova", dial:"+373", flag:"🇲🇩" },
    { name:"Monaco", dial:"+377", flag:"🇲🇨" },
    { name:"Montenegro", dial:"+382", flag:"🇲🇪" },
    { name:"Morocco", dial:"+212", flag:"🇲🇦" },
    { name:"Netherlands", dial:"+31", flag:"🇳🇱" },
    { name:"New Zealand", dial:"+64", flag:"🇳🇿" },
    { name:"North Macedonia", dial:"+389", flag:"🇲🇰" },
    { name:"Norway", dial:"+47", flag:"🇳🇴" },
    { name:"Oman", dial:"+968", flag:"🇴🇲" },
    { name:"Pakistan", dial:"+92", flag:"🇵🇰" },
    { name:"Poland", dial:"+48", flag:"🇵🇱" },
    { name:"Portugal", dial:"+351", flag:"🇵🇹" },
    { name:"Qatar", dial:"+974", flag:"🇶🇦" },
    { name:"Romania", dial:"+40", flag:"🇷🇴" },
    { name:"Russia", dial:"+7", flag:"🇷🇺" },
    { name:"Saudi Arabia", dial:"+966", flag:"🇸🇦" },
    { name:"Serbia", dial:"+381", flag:"🇷🇸" },
    { name:"Singapore", dial:"+65", flag:"🇸🇬" },
    { name:"Slovakia", dial:"+421", flag:"🇸🇰" },
    { name:"Slovenia", dial:"+386", flag:"🇸🇮" },
    { name:"South Africa", dial:"+27", flag:"🇿🇦" },
    { name:"South Korea", dial:"+82", flag:"🇰🇷" },
    { name:"Spain", dial:"+34", flag:"🇪🇸" },
    { name:"Sri Lanka", dial:"+94", flag:"🇱🇰" },
    { name:"Sweden", dial:"+46", flag:"🇸🇪" },
    { name:"Switzerland", dial:"+41", flag:"🇨🇭" },
    { name:"Syria", dial:"+963", flag:"🇸🇾" },
    { name:"Tajikistan", dial:"+992", flag:"🇹🇯" },
    { name:"Tunisia", dial:"+216", flag:"🇹🇳" },
    { name:"Turkmenistan", dial:"+993", flag:"🇹🇲" },
    { name:"Türkiye", dial:"+90", flag:"🇹🇷" },
    { name:"Ukraine", dial:"+380", flag:"🇺🇦" },
    { name:"United Arab Emirates", dial:"+971", flag:"🇦🇪" },
    { name:"United Kingdom", dial:"+44", flag:"🇬🇧" },
    { name:"United States", dial:"+1", flag:"🇺🇸" },
    { name:"Uzbekistan", dial:"+998", flag:"🇺🇿" }
  ];

  const select = document.querySelector('select[name="country_code"]');
  if (!select) return;

  const sorted = countries.slice().sort((a,b) =>
    a.name.localeCompare(b.name, 'en', { sensitivity: 'base' })
  );

  select.innerHTML = "";
  for (const c of sorted) {
    const opt = document.createElement("option");
    opt.value = c.dial;
    opt.textContent = `${c.flag} ${c.dial} — ${c.name}`;
    if (c.name === "Türkiye") opt.selected = true;
    select.appendChild(opt);
  }
  if (!select.value) select.value = "+90";
});

// -------------------- Language modal + Google Translate --------------------
(function(){
  function loadTranslate(cb){
    if (window.google && window.google.translate) return cb();
    var s=document.createElement('script');
    s.src='https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    window.googleTranslateElementInit=function(){
      new google.translate.TranslateElement({
        pageLanguage:'en',
        includedLanguages:'af,sq,ar,az,eu,bn,bg,ca,zh-CN,hr,cs,da,nl,et,fi,fr,gl,ka,de,el,iw,hi,hu,is,it,ja,ko,lv,mk,ms,fa,pl,pt,ro,ru,sk,en,tr',
        autoDisplay:false
      }, 'google_translate_element');
      cb();
    };
    document.head.appendChild(s);
  }

  function whenComboReady(fn, timeoutMs=10000){
    const start=Date.now();
    (function tick(){
      const sel=document.querySelector('#google_translate_element select.goog-te-combo');
      if (sel) return fn(sel);
      if (Date.now()-start>timeoutMs) return console.warn('Translate combo not found.');
      setTimeout(tick,150);
    })();
  }

  const modal = document.getElementById('lang-modal');
  function openModal(){
    modal.setAttribute('aria-hidden','false');
    setTimeout(()=>{ const first=modal.querySelector('.lang-item'); if(first) first.focus(); },0);
  }
  function closeModal(){ modal.setAttribute('aria-hidden','true'); }

  document.addEventListener('click', (e)=>{
    const pill = e.target.closest('.lang-pill');
    if (pill) { e.preventDefault(); openModal(); return; }
    const closer = e.target.closest('[data-close]');
    if (closer) { e.preventDefault(); closeModal(); return; }
  });

  function applyLanguage(lang){
    whenComboReady((sel)=>{
      sel.value = lang;
      sel.dispatchEvent(new Event('change'));
      try{ localStorage.setItem('site_lang', lang); }catch(_){}
      updatePill(lang);
      closeModal();
    });
  }

  const pillEl = document.querySelector('.lang-pill');
  function updatePill(lang){
    if(!pillEl) return;
    const map = {
      af:{flag:'🇿🇦',code:'Af'}, sq:{flag:'🇦🇱',code:'Sq'}, ar:{flag:'🇪🇬',code:'Ar'}, az:{flag:'🇦🇿',code:'Az'},
      eu:{flag:'🇪🇸',code:'Eu'}, bn:{flag:'🇧🇩',code:'Bn'}, bg:{flag:'🇧🇬',code:'Bg'}, ca:{flag:'🇦🇩',code:'Ca'},
      'zh-CN':{flag:'🇨🇳',code:'Zh'}, hr:{flag:'🇭🇷',code:'Hr'}, cs:{flag:'🇨🇿',code:'Cs'}, da:{flag:'🇩🇰',code:'Da'},
      nl:{flag:'🇳🇱',code:'Nl'}, et:{flag:'🇪🇪',code:'Et'}, fi:{flag:'🇫🇮',code:'Fi'}, fr:{flag:'🇫🇷',code:'Fr'},
      gl:{flag:'🇪🇸',code:'Gl'}, ka:{flag:'🇬🇪',code:'Ka'}, de:{flag:'🇩🇪',code:'De'}, el:{flag:'🇬🇷',code:'El'},
      iw:{flag:'🇮🇱',code:'He'}, hi:{flag:'🇮🇳',code:'Hi'}, hu:{flag:'🇭🇺',code:'Hu'}, is:{flag:'🇮🇸',code:'Is'},
      it:{flag:'🇮🇹',code:'It'}, ja:{flag:'🇯🇵',code:'Ja'}, ko:{flag:'🇰🇷',code:'Ko'}, lv:{flag:'🇱🇻',code:'Lv'},
      mk:{flag:'🇲🇰',code:'Mk'}, ms:{flag:'🇲🇾',code:'Ms'}, fa:{flag:'🇮🇷',code:'Fa'}, pl:{flag:'🇵🇱',code:'Pl'},
      pt:{flag:'🇵🇹',code:'Pt'}, ro:{flag:'🇷🇴',code:'Ro'}, ru:{flag:'🇷🇺',code:'Ru'}, sk:{flag:'🇸🇰',code:'Sk'},
      en:{flag:'🇬🇧',code:'En'}, tr:{flag:'🇹🇷',code:'Tr'}
    };
    const m = map[lang] || map.en;
    pillEl.innerHTML = '<span class="flag">'+m.flag+'</span><span class="code">'+m.code+'</span>';
  }

  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('.lang-item');
    if(!btn) return;
    e.preventDefault();
    const lang = btn.getAttribute('data-lang') || 'en';
    applyLanguage(lang);
  });

  loadTranslate(function(){
    const saved = (localStorage.getItem('site_lang') || 'en').toLowerCase();
    updatePill(saved);
    if(saved !== 'en'){ applyLanguage(saved); }
  });
})();
