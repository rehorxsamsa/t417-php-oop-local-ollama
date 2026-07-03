# DEMO – Jak tuto aplikaci prezentovat úplnému laikovi

> Scénář prezentace pro člověka, který **nezná umělou inteligenci (AI)**, neví, co je
> **MCP**, a neslyšel o asistentovi **Claude**. Cílem je, aby po ~15–20 minutách chápal,
> **co aplikace dělá, proč je to zajímavé a že je to bezpečné a srozumitelné**.
>
> Text je psaný tak, že levý sloupec je *co říct nahlas* a odsazené bloky jsou
> *co udělat / na co ukázat*. Nemusíš číst doslova — ber to jako osnovu.

---

## 0) Než začneš (příprava, 5 min předem)

- [ ] Aplikace běží: `docker compose up -d` a v prohlížeči je otevřené <http://localhost:8080>.
- [ ] Model je stažený (poprvé to trvá pár minut): `docker compose logs -f ollama-pull`
      musí skončit hláškou o dokončení.
- [ ] Otevři si druhé okno/terminál pro CLI ukázku (volitelné, pro „technické" publikum).
- [ ] **Vypni internet na chvíli** (nebo to jen zmiň) — je to nejsilnější moment celé
      prezentace: aplikace funguje i **bez připojení k internetu**.
- [ ] Připrav si dopředu 2–3 věty textu k vložení (např. odstavec z novinového článku).

> 💡 Tip: nejdřív si celé demo jednou projdi sám, ať víš, jak rychle model odpovídá.
> Na slabším počítači může odpověď trvat několik sekund — to je normální, jen o tom
> dopředu řekni, ať to nevypadá jako chyba.

---

## 1) Úplný začátek: co je to vlastně „AI" (2 min)

Publikum nezná AI, takže první minuty jsou o **odbourání strachu a mýtů**.

Řekni zhruba tohle:

> „Ukážu vám program, který si umí *povídat* — odpoví na otázku, přeloží větu, shrne
> dlouhý text. Funguje to díky takzvanému **jazykovému modelu**. Představte si ho jako
> **velmi sečtělého pomocníka, který přečetl obrovské množství textů** a naučil se z nich,
> jak na sebe slova navazují. Není to živá bytost, nemyslí ani necítí — je to program,
> který **odhaduje nejpravděpodobnější pokračování textu**. Přesně jako když vám mobil
> napovídá další slovo při psaní SMS, jen mnohonásobně chytřeji."

Klíčové věty, které chceš, aby si zapamatovali:

- **Není to kouzlo** — je to statistika a hodně přečteného textu.
- **Nemá vlastní vůli** — jen odpovídá na to, co dostane.
- **Může se splést** — občas si něco „vymyslí". To je vlastnost, ne rozbitý program.

---

## 2) Tři pojmy, které publikum plete (2 min)

Protože zadání zmiňuje, že člověk nezná „AI, MCP ani Claude", krátce je **odděl**, aby
v tom nebyl zmatek. Stačí jedna věta na každý:

| Pojem | Vysvětlení laikovi | Souvisí s naší aplikací? |
|------|--------------------|--------------------------|
| **AI / jazykový model** | Program, který umí pracovat s textem (odpovídat, překládat, shrnovat). | **ANO** – to je jádro dema. |
| **Claude** | Konkrétní placený AI asistent od firmy Anthropic, běží na internetu (v „cloudu"). | **NE** – náš program ho nepoužívá. |
| **MCP** | Technický „konektor", kterým se AI napojuje na další nástroje a data. Pokročilá věc. | **NE** – v tomto demu ho nepotřebujeme. |

Pointa, kterou řekni nahlas:

> „Možná jste slyšeli jména jako ChatGPT nebo Claude — to jsou služby na internetu,
> kterým posíláte svoje data na cizí servery. **Naše aplikace je jiná: celý AI model
> běží tady, na tomhle počítači.** Nic neposílá ven. Za chvíli vám to i dokážu."

Tím jsi elegantně vyřešil, že publikum ty pojmy nezná — **řekl jsi jim, že je pro dnešek
nepotřebují**, a rovnou nabídl hlavní devízu: soukromí a lokální běh.

---

## 3) Co uvidí na obrazovce (1 min)

Přepni na <http://localhost:8080>.

> „Tohle je celá aplikace. Je to webová stránka se **sedmi tlačítky — sedmi ukázkami**,
> co všechno s takovým modelem jde dělat. Vždycky něco napíšu do políčka, kliknu a dole
> se objeví odpověď. Pojďme si projít ty nejnázornější."

Ukaž prstem/myší na seznam sedmi příkladů, ať publikum vidí, že je toho víc, ale že to
budeme brát postupně.

---

## 4) Živé ukázky (hlavní část, 8–10 min)

Nedělej všech 7 — **vyber 3 až 4**, které mají největší „aha efekt". Doporučené pořadí:

### Ukázka A — Otázka a odpověď (příklad 1)  ⭐ začni tímhle

> „Nejjednodušší věc: zeptám se ho na cokoliv."

    Do pole zadej: „Co je to Docker a k čemu se používá?"
    Klikni na spuštění a nech odpověď doběhnout.

Komentář nahlas:

> „Vidíte? Odpověděl vlastními slovy, ne že by našel hotovou stránku jako Google.
> **Sám tu větu poskládal.**"

### Ukázka B — Překlad (příklad 3)  ⭐ nejlépe pochopitelné

> „Umí i překládat, oběma směry."

    Zadej českou větu, např.: „Dnes je krásné počasí a jdu na procházku."
    Ukaž anglický výsledek. Pak zkus obráceně anglickou větu.

> „Žádný slovník, žádné pravidlo `když-tak`. Naučil se to z textů, které viděl."

### Ukázka C — Shrnutí textu (příklad 2)  ⭐ „praktický přínos"

> „Tohle lidi ocení v praxi: vezme dlouhý text a udělá z něj pár vět."

    Vlož připravený delší odstavec (článek, e-mail).
    Ukaž, že výstup jsou 2–3 věty.

> „Představte si, že takhle proženete dlouhý e-mail nebo zápis z porady."

### Ukázka D — Chat s pamětí (příklad 6)  ⭐ efektní finále

> „A umí si i pamatovat, o čem se bavíme."

    Ukaž, že konverzace má více kol a model navazuje na předchozí větu.

> „Tohle je základ všech chatovacích asistentů — drží si kontext rozhovoru."

**Volitelně pro techničtější publikum:** příklad 4 (Analýza sentimentu → vrátí
strukturovaný JSON) a příklad 7 (Generátor kódu → napíše PHP funkci). Ukazují, že
výstup nemusí být jen „text pro člověka", ale i **data pro další program**.

> ⚠️ Když se model náhodou splete nebo napíše nesmysl, **nezakrývej to** — otoč to
> v poučení: „Vidíte, tohle je ta vlastnost, o které jsem mluvil — občas si vymýšlí.
> Proto se výstup u důležitých věcí vždy kontroluje."

---

## 5) Trumf: „a teď vypnu internet" (2 min)

Tohle je moment, který si lidé zapamatují nejvíc.

    Odpoj Wi-Fi / vytáhni kabel (nebo to zmiň, pokud jsi offline už od začátku).
    Spusť znovu ukázku A (polož jinou otázku).

> „Internet je pryč — a ono to **pořád funguje**. Protože ten ‚chytrý pomocník'
> **není nikde na internetu, běží celý tady v počítači.** To znamená: vaše data,
> vaše dokumenty, vaše otázky **nikam neodcházejí.** Nikdo je nevidí, nic se
> neodesílá na cizí servery. Pro firmy a citlivé údaje je tohle zásadní."

Shrnutí výhody třemi slovy: **soukromí, žádné poplatky, žádná závislost na internetu.**

---

## 6) Jak to funguje „pod kapotou" (volitelné, 2 min)

Jen když publikum chce vědět víc. Drž se přirovnání, ne technických termínů.

> „Uvnitř jsou vlastně **dvě části**:
>
> 1. **Model** — ten ‚sečtělý pomocník'. Je to soubor o velikosti pár set megabajtů,
>    stažený jednou do počítače. Stará se o něj program jménem **Ollama**.
> 2. **Naše aplikace** — napsaná v jazyce **PHP**. Ta dělá to hezké okno, tlačítka a
>    posílá modelu vaše otázky. Je to jako **číšník**: vezme od vás objednávku, donese
>    ji do kuchyně (modelu) a přinese hotový talíř (odpověď).
>
> A celé to běží v **Dockeru** — což je způsob, jak zabalit program se vším, co
> potřebuje, aby fungoval stejně na každém počítači. Jako **přepravní kontejner**:
> je jedno, na jakou loď ho naložíte, uvnitř je vždy totéž."

Pokud máš technické publikum, ukaž na chvíli terminál:

    docker compose exec php php src/cli.php 1 "Co je Docker?"

> „Tohle je ta samá věc bez okna, ovládaná z příkazové řádky. Ať web nebo terminál —
> uvnitř je stejný kód, sedm samostatných tříd, čistý objektový PHP bez frameworku."

---

## 7) Časté otázky publika (připrav si odpovědi)

| Otázka | Odpověď v kostce |
|--------|------------------|
| „Špehuje mě to? Posílá to něco ven?" | Ne. Model běží lokálně, offline demo to dokazuje. |
| „Je to zadarmo?" | Ano, model i software jsou open-source, žádné poplatky za dotaz. |
| „Nahradí to lidi?" | Je to nástroj — pomůže s rutinou (překlad, shrnutí), ale výstup je třeba kontrolovat. |
| „Je to stejné jako ChatGPT?" | Princip ano, ale ChatGPT běží na cizích serverech a je mnohem větší. Tohle je malý model běžící u vás. |
| „Proč je to někdy pomalé / hloupější?" | Použili jsme záměrně **malý** model (~400 MB), aby běžel i na slabém počítači. Větší modely jsou chytřejší, ale náročnější. |
| „Může se to splést?" | Ano, občas si ‚vymyslí' — proto se to hodí jako pomocník, ne jako neomylný zdroj pravdy. |

---

## 8) Závěr (1 min)

> „Takže: viděli jste program, který si umí povídat, překládat a shrnovat — a to celé
> **u vás v počítači, bez internetu, zadarmo a soukromě**. Není to kouzlo ani vědomí,
> je to šikovný nástroj. A protože běží lokálně, máte plnou kontrolu nad tím, co s ním
> děláte a kam vaše data jdou — nikam."

Jednou větou, kterou si mají odnést:

> **„Umělá inteligence nemusí být tajemná služba někde na internetu — může to být
> obyčejný program, který si spustíte doma a plně mu rozumíte."**

---

## Rychlá tahák-osnova (vytiskni si)

1. **AI = sečtělý pomocník**, ne kouzlo, ne vědomí.
2. **Claude / MCP dnes nepotřebujeme** — tohle běží lokálně.
3. Ukázat web → **7 příkladů**.
4. Živě: **otázka → překlad → shrnutí → chat s pamětí**.
5. **Vypnout internet** → pořád funguje → soukromí.
6. (Volitelně) pod kapotou: model + PHP číšník + Docker kontejner.
7. FAQ podle publika.
8. Závěr: **AI, kterou máte doma a rozumíte jí.**

---

### Technická příloha (jen pro tebe, ne pro publikum)

```bash
# Start
docker compose up -d
docker compose logs -f ollama-pull      # sleduj stažení modelu (poprvé)

# Web
#   http://localhost:8080

# CLI ukázky
docker compose exec php php src/cli.php list
docker compose exec php php src/cli.php 1 "Co je Docker?"
docker compose exec php php src/cli.php 3 "Dobrý den, jak se máte?"

# Stop
docker compose down
```

- Výchozí model: `qwen2.5:0.5b` (~400 MB), lze změnit v `.env` (proměnná `LLM_MODEL`).
- Pokud odpovědi trvají dlouho, je to CPU + malý počítač — ne chyba. Zvaž předem
      „zahřát" model jedním dotazem před prezentací, aby první odpověď nebyla nejpomalejší.
</content>
</invoke>
