(()=>{"use strict";!function(){const e=document.querySelector(".wp-block-bookwyrm-blocks-bookwyrm-read-block");let t=e.getAttribute("data-user"),o=e.getAttribute("data-instance").replace(/(http(s)?:\/\/)|(\/+$)/g,"").replace(/\/+/g,"/");if(!t||""==t||null==t||!o||""==o||null==o)return void function(e){const t=document.createElement("p");t.textContent="⚠️ Sorry, there has been an error fetching the feed.",t.style.fontWeight="bold";const o=document.querySelector(e);document.querySelector(e).style.display="block",o.appendChild(t)}("div.reading--list");fetch(`https://corsproxy.io/?https%3A%2F%2F${o}%2Fuser%2F${t}%2Fshelf%2Freading.json%3Fpage%3D1`).then((e=>e.json())).then((e=>{e.orderedItems.forEach(r)})).catch((e=>{throw e}));const r=async e=>{let t=(e.isbn13,document.createElement("div")),o=await n(e.authors[0]);var r;t.classList.add(`book-${e.isbn13}`),t.innerHTML=`<img src=${r=e.isbn13,`https://covers.openlibrary.org/b/isbn/${r}-L.jpg`} width="150" height="225" alt="cover ${e.title}" loading="lazy" style="border: 1px solid #ccc; background-color: #eee;"  ><p><b><cite>${e.title}</cite></b><br />${o}</p>`,document.querySelector(".reading--list").appendChild(t)},n=async e=>fetch(`https://corsproxy.io/?${e}.json`).then((e=>e.json())).then((e=>`by ${e.name}`)).catch((e=>{throw e}))}()})();