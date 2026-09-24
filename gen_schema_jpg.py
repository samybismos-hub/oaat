# -*- coding: utf-8 -*-
from PIL import Image, ImageDraw, ImageFont
import os
OUT = r"C:\laragon\www\oaat\schema-oaat-v2.jpg"
W = 3000
MARGIN = 50
GAP = 40
BOX_W = (W - 2*MARGIN - 2*GAP)//3
ROW_H = 34
TOP = 300
def font(bold=False, size=22):
    p = r"C:\Windows\Fonts\arialbd.ttf" if bold else r"C:\Windows\Fonts\arial.ttf"
    try:
        return ImageFont.truetype(p, size)
    except Exception:
        return ImageFont.load_default()
F_TITLE = font(True, 56)
F_SUB = font(False, 26)
F_TN = font(True, 29)
F_ROLE = font(False, 20)
F_COL = font(False, 22)
F_FB = font(True, 26)
F_F = font(False, 24)
TA = [
 dict(n="users", r="Comptes admin CMS. Roles via Spatie.", c=(55,65,81),
  cols=[("id","PK"),("name","varchar"),("email","varchar UNIQUE"),("password","hash bcrypt"),("timestamps","created/updated")]),
 dict(n="pages *NOUVEAU*", r="Statiques: Mission, Vision, Valeurs, Historique, mentions.", c=(13,148,136),
  cols=[("id","PK"),("slug","varchar UNIQUE"),("title (T)","json fr/en"),("body (T) riche","json HTML WYSIWYG"),("published_at","NULL=brouillon"),("timestamps","")]),
 dict(n="settings *NOUVEAU*", r="Globaux: coordonnees, localisation, reseaux, chiffres.", c=(13,148,136),
  cols=[("id","PK"),("key","varchar UNIQUE"),("value","json"),("group","contact|reseaux|chiffres")]),
 dict(n="team_members *NOUVEAU*", r="Equipe / gouvernance (page Organisation).", c=(13,148,136),
  cols=[("id","PK"),("name","varchar"),("role (T)","json fr/en"),("photo","media"),("bio (T)","json"),("position","int ordre"),("published_at","datetime")]),
 dict(n="domains", r="4 domaines, PAGE DEDIEE par domaine + SEO.", c=(37,99,235),
  cols=[("id","PK"),("name (T)","json fr/en"),("slug (T)","json UNIQUE/lang"),("description (T)","json"),("objectives (T)","json"),("activities (T)","json"),("image","media"),("position","int ordre"),("published_at","datetime"),("meta_* (T)","json SEO")]),
 dict(n="projects [COEUR]", r="Fiches detaillees: statut, periode, beneficiaires, resultats.", c=(37,99,235),
  cols=[("id","PK"),("domain_id","FK -> domains.id"),("title (T)","json fr/en"),("slug (T)","json UNIQUE/lang"),("status","planifie|en_cours|acheve"),("country/city","varchar"),("latitude/longitude","decimal CARTE"),("start/end_date","date periode"),("summary (T)","json"),("objectives/activities (T)","json"),("beneficiaries/results (T)","json"),("cover_image","media og:image"),("published_at","datetime"),("meta_* (T)","json SEO")]),
]
TB = [
 dict(n="project_partner", r="Pivot N-N Projet <> Partenaire.", c=(100,116,139),
  cols=[("project_id","FK -> projects"),("partner_id","FK -> partners"),("PK","composite (project,partner)")]),
 dict(n="partners", r="Partenaires + collaborations (category=vocab client).", c=(37,99,235),
  cols=[("id","PK"),("name","varchar"),("category","bailleur|technique|instit."),("logo","media"),("website","url"),("description (T)","json"),("published_at","datetime")]),
 dict(n="actualities", r="Articles, annonces, nouvelles, communiques.", c=(124,58,237),
  cols=[("id","PK"),("author_id","FK -> users"),("title (T)","json fr/en"),("slug (T)","json UNIQUE/lang"),("excerpt (T)","json"),("body (T) riche","json HTML"),("cover_image","media og:image"),("published_at","datetime"),("meta_* (T)","json SEO")]),
 dict(n="documents", r="Rapports, etudes, guides (FR/EN).", c=(13,148,136),
  cols=[("id","PK"),("title (T)","json fr/en"),("type","rapport|etude|guide"),("lang","fr|en|both"),("file","via media"),("published_at","datetime")]),
 dict(n="albums", r="Galerie photos/videos liees aux projets.", c=(13,148,136),
  cols=[("id","PK"),("title (T)","json fr/en"),("description (T)","json"),("project_id","FK NULL -> projects"),("medias","via media"),("published_at","datetime")]),
 dict(n="media (Spatie)", r="Fichiers polymorphes mutualises.", c=(100,116,139),
  cols=[("id","PK"),("model_type/model_id","polymorphe"),("cibles","Project|Actuality|Album|Need"),("collection/file/mime/size","fichier"),("custom_props","legende, credit")]),
 dict(n="contact_messages", r="Boite reception formulaire Contact.", c=(100,116,139),
  cols=[("id","PK"),("name/email","varchar"),("subject/message","text"),("handled","bool traite?"),("created_at","datetime")]),
 dict(n="need_requests", r="Demandes structurees MVP (besoin/projet/collab).", c=(220,38,38),
  cols=[("id","PK"),("type","besoin|projet|collaboration"),("organization","varchar"),("contact_name","varchar"),("email/phone","varchar"),("location","varchar"),("description","text"),("fichiers","via media"),("status","nouveau|en_cours|traite")]),
]
ALL = TA + TB
def wrap(dr, txt, fnt, maxw):
    words, out, cur = txt.split(), [], ""
    for w in words:
        t = (cur+" "+w).strip()
        try:
            wd = dr.textlength(t, font=fnt)
        except Exception:
            wd = len(t)*11
        if wd <= maxw:
            cur = t
        else:
            out.append(cur); cur = w
    if cur:
        out.append(cur)
    return out
tmp = Image.new("RGB", (W, 50), "white")
dd = ImageDraw.Draw(tmp)
def box_h(t):
    rl = wrap(dd, "ROLE: "+t["r"], F_ROLE, BOX_W-36)
    return 62+len(rl)*24+10+len(t["cols"])*ROW_H+16
order = sorted(range(len(ALL)), key=lambda i: box_h(ALL[i]), reverse=True)
cols_h = [TOP, TOP, TOP]
assign = [0]*len(ALL)
for i in order:
    c = cols_h.index(min(cols_h))
    assign[i] = c
    cols_h[c] += box_h(ALL[i])+28
FOOT = 430
H = max(cols_h)+FOOT+40
img = Image.new("RGB", (W, H), "white")
d = ImageDraw.Draw(img)
d.rectangle([0, 0, W, 240], fill=(15, 23, 42))
d.text((MARGIN, 26), "OAAT - MODELE DE DONNEES v2 (MVP)", font=F_TITLE, fill="white")
d.text((MARGIN, 108), "14 tables  |  FR/EN en JSON (T)  |  publication par published_at  |  carte=OUI  |  domaines=pages dediees  |  partners.category=OUI", font=F_SUB, fill=(203, 213, 225))
d.text((MARGIN, 168), "LEGENDE : (T)=traduisible JSON  |  UQ=unique  |  FK=cle etrangere  |  *NOUVEAU*=ajout critique v2", font=F_SUB, fill=(100, 116, 139))
d.text((MARGIN, 252), "Lecture : bleu=coeur metier  |  violet=contenus FR/EN  |  vert=ressources  |  gris=technique  |  rouge=entrees (formulaires)", font=F_SUB, fill=(100, 116, 139))
cur = [TOP, TOP, TOP]
for i, t in enumerate(ALL):
    c = assign[i]
    x0 = MARGIN+c*(BOX_W+GAP)
    y0 = cur[c]
    hh = box_h(t)
    x1, y1 = x0+BOX_W, y0+hh
    d.rounded_rectangle([x0, y0, x1, y1], radius=18, fill=(248, 250, 252), outline=t["c"], width=4)
    d.rounded_rectangle([x0, y0, x1, y0+58], radius=18, fill=t["c"])
    d.rectangle([x0, y0+30, x1, y0+58], fill=t["c"])
    d.text((x0+18, y0+10), t["n"].upper(), font=F_TN, fill="white")
    ry = y0+58+8
    for ln in wrap(d, "ROLE: "+t["r"], F_ROLE, BOX_W-36):
        d.text((x0+18, ry), ln, font=F_ROLE, fill=(15, 23, 42))
        ry += 24
    ry += 6
    d.line([x0+18, ry, x1-18, ry], fill=t["c"], width=2)
    ry += 8
    for cn, ct in t["cols"]:
        d.text((x0+22, ry), "* "+cn, font=F_COL, fill=(30, 41, 59))
        d.text((x0+BOX_W//2+10, ry), ct, font=F_COL, fill=(100, 116, 139))
        ry += ROW_H
    cur[c] += hh+28
fy = max(cur)+16
d.rectangle([MARGIN, fy, W-MARGIN, fy+FOOT-30], fill=(15, 23, 42))
d.text((MARGIN+24, fy+14), "RELATIONS & REGLES", font=F_FB, fill=(250, 204, 21))
FL = [
 "* Domain 1 ---- N Project   |   Project N <---> N Partner (via project_partner)",
 "* Project 1 ---- N Album (nullable)   |   User 1 ---- N Actuality (auteur)",
 "* Media N ---- 1 polymorphe vers Project | Actuality | Album | NeedRequest",
 "* Publication unifiee : published_at NULL = brouillon (fini is_active / status redondants)",
 "* Contacts : handled (bool). PAS d'IP stockee (RGPD). Need: statut nouveau > en_cours > traite.",
 "* 14 colonnes inventees SUPPRIMEES (icon, type, ip, budget, indicateurs...) + 4 contenus ajoutes.",
]
yy = fy+56
for ln in FL:
    for wln in wrap(d, ln, F_F, W-2*MARGIN-60):
        d.text((MARGIN+24, yy), wln, font=F_F, fill="white")
        yy += 32
img.save(OUT, "JPEG", quality=92)
print("OK", OUT, img.size)

