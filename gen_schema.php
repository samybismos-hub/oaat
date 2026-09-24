<?php
$W=3200;$H=4400;
$img=imagecreatetruecolor($W,$H);
$white=imagecolorallocate($img,255,255,255);
$ink=imagecolorallocate($img,30,30,30);
$muted=imagecolorallocate($img,110,110,110);
$line=imagecolorallocate($img,200,200,200);
$accent=imagecolorallocate($img,24,60,110);
$hdrTx=imagecolorallocate($img,255,255,255);
$cSocle=imagecolorallocate($img,219,234,249);
$cMetier=imagecolorallocate($img,220,245,225);
$cCont=imagecolorallocate($img,255,238,210);
$cEntr=imagecolorallocate($img,235,220,248);
$cPivot=imagecolorallocate($img,230,230,230);
imagefilledrectangle($img,0,0,$W,$H,$white);
$font='C:\\Windows\\Fonts\\arial.ttf';
$fontB='C:\\Windows\\Fonts\\arialbd.ttf';
function txt($img,$s,$x,$y,$c,$str,$b=false){
 global $font,$fontB;
 $f=$b?$fontB:$font;
 imagettftext($img,$s,0,$x,$y,$c,$f,$str);
}
imagefilledrectangle($img,0,0,$W,210,$accent);
txt($img,44,60,85,$hdrTx,"OAAT - Modele de donnees v2 (MVP)",true);
txt($img,24,60,135,$hdrTx,"14 tables | FR/EN=JSON {fr,en} | (T)=traduisible | FK=cle etrangere | U=unique",false);
txt($img,22,60,175,$hdrTx,"Decisions : 1) Carte OUI->lat/long 2) 1 page/domaine->slug+SEO 3) partners.category",false);
$ly=240;
$groups=[["Socle/Admin",$cSocle],["Coeur metier",$cMetier],["Contenus",$cCont],["Entrees",$cEntr]];
$lx=60;
foreach($groups as $g){
 imagefilledrectangle($img,$lx,$ly,$lx+36,$ly+28,$g[1]);
 imagerectangle($img,$lx,$ly,$lx+36,$ly+28,$ink);
 txt($img,20,$lx+48,$ly+24,$ink,$g[0],false);
 $lx+=400;
}
txt($img,20,1700,$ly+24,$muted,"Publication : published_at (NULL=brouillon) | Media=polymorphe",false);
$tables=[
["users|AUTH - Comptes admin et roles|0","id PK","name","email U","password (hashe)","roles: admin / editeur","timestamps"],
["pages|CONTENU FIXE - Pages institutionnelles|0","id PK","slug U (mission, vision, valeurs, historique)","title (T)","body (T) riche WYSIWYG","published_at","timestamps"],
["settings|REGLAGES - Parametres globaux|0","id PK","key U (tel, email, adresse, reseaux)","value JSON (chiffres accueil)","timestamps"],
["team_members|EQUIPE - Gouvernance|0","id PK","name","role (T)","photo (media)","bio (T)","position (ordre)","published_at"],
["domains|METIER - 4 domaines, pages dediees|1","id PK","name (T)","slug (T) U -> /domaines/{slug}","description (T)","objectives (T) [spec]","activities (T) [spec]"],
["domains2|METIER (suite)|1","image","position (ordre)","published_at","meta_title (T), meta_description (T)"],
["projects|COEUR - Fiches projets|1","id PK","domain_id FK -> domains.id","title (T)","slug (T) U","status: planifie|en_cours|acheve","country, city"],
["projects2|COEUR (suite)|1","latitude, longitude [carte]","start_date, end_date [periode]","summary (T)","objectives, activities, beneficiaires (T)"],
["projects3|COEUR (suite)|1","results (T) [spec]","cover_image (og:image WhatsApp/FB)","published_at","meta_title (T), meta_description (T)"],
["partners|METIER - Partenaires|1","id PK","name","category (vocab. client)","logo, website","description (T)","published_at"],
["project_partner|PIVOT N-N projet-partenaire|4","project_id FK -> projects.id","partner_id FK -> partners.id","PK composite (project_id, partner_id)"],
["actualities|CONTENU DATE - Articles/annonces|2","id PK","author_id FK -> users.id","title (T)","slug (T) U","excerpt (T)"],
["actualities2|CONTENU DATE (suite)|2","body (T) riche WYSIWYG (= article)","cover_image (og:image)","published_at (NULL=brouillon)","meta_title (T), meta_description (T)"],
["documents|DOCS - Rapports/etudes/guides|2","id PK","title (T)","type: rapport|etude|guide","lang: fr|en|both","file (via media)","published_at"],
["albums|GALERIE - Albums photos/videos|2","id PK","title (T)","description (T)","project_id FK nullable -> projects.id","published_at","photos/videos via media"],
["media|FICHIERS - Stockage polymorphe|2","id PK","model_type + model_id (polymorphe)","collection_name, file_name, mime_type","custom_properties JSON (legende, credit)","size calcule, jamais saisi"],
["contact_messages|ENTREE - Formulaire Contact|3","id PK","name, email, subject, message","handled bool (traite oui/non)","created_at","ANTI-SPAM: honeypot+throttle (IP non stockee)"],
["need_requests|ENTREE MVP - Soumettre un besoin|3","id PK","type: besoin|projet|collaboration","organization, contact_name, email, phone","location, description"],
["need_requests2|ENTREE MVP (suite)|3","fichiers joints (via media)","status: nouveau|en_cours|traite","created_at"],
];
$palette=[$cSocle,$cMetier,$cCont,$cEntr,$cPivot];
$colX=[60,1630];$colW=1510;$y0=320;$gap=24;
$ys=[$y0,$y0];$ci=0;
foreach($tables as $t){
 $head=array_shift($t);
 [$name,$role,$pi]=explode("|",$head);
 $bg=$palette[(int)$pi];
 $c=$ci%2;$x=$colX[$c];$y=$ys[$c];
 $rh=64;$ch=34*count($t)+14;$h=$rh+$ch+18;
 imagefilledrectangle($img,$x,$y,$x+$colW,$y+$h,$bg);
 imagerectangle($img,$x,$y,$x+$colW,$y+$h,$ink);
 imagefilledrectangle($img,$x,$y,$x+$colW,$y+$rh,$accent);
 txt($img,26,$x+18,$y+30,$hdrTx,$name,true);
 txt($img,19,$x+18,$y+56,$hdrTx,"Role : ".$role,false);
 $cy=$y+$rh+30;
 foreach($t as $col){ txt($img,20,$x+24,$cy,$ink,"- ".$col,false);$cy+=34; }
 $ys[$c]=$y+$h+$gap;$ci++;
}
$bottom=max($ys[0],$ys[1])+10;
imagefilledrectangle($img,0,$bottom,$W,$H,$white);
imagerectangle($img,40,$bottom+10,$W-40,$H-40,$line);
txt($img,28,70,$bottom+60,$ink,"Relations (FK)",true);
$rels=[
"domains 1 --- N projects (projects.domain_id -> domains.id)",
"projects N <---> N partners via project_partner",
"projects 1 --- N albums (albums.project_id nullable)",
"users 1 --- N actualities (author_id = auteur)",
"media N --- 1 polymorphe (-> projects|actualities|albums|...)",
"contact_messages + need_requests : entrees SANS FK (pas de compte visiteur)",
];
$ry=$bottom+105;
foreach($rels as $r){ txt($img,21,90,$ry,$ink,"- ".$r,false);$ry+=38; }
$ry+=8;
txt($img,28,70,$ry,$ink,"Regles v2 (suite a ta critique)",true);$ry+=45;
$rules=[
"SUPPRIME : domains.icon | partners.type | actualities.status+category | contact.ip+status | is_active | budget+consent | indicateurs | documents.size",
"UNIFIE : publication = published_at (NULL = brouillon).",
"AJOUTE (doc client, oublie v1) : pages + settings + team_members.",
"GARDE : body (article) | cover_image (WhatsApp/FB) | status projets | lat/long | need_requests | category.",
"REGLE : toute colonne repond parce que tire du doc client, sinon elle disparait.",
];
foreach($rules as $r){ txt($img,20,90,$ry,$ink,"- ".$r,false);$ry+=36; }
$out='C:\\laragon\\www\\oaat\\schema-oaat-v2.jpg';
imagejpeg($img,$out,92);
imagedestroy($img);
echo "OK -> $out";

