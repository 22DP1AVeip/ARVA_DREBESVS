<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message'  => ['required', 'string', 'max:500'],
            'history'  => ['nullable', 'array', 'max:20'],
        ]);

        $products = Product::all(['name', 'price', 'category', 'gender']);

        $catalog = $products->map(fn($p) =>
            "- {$p->name} | Kategorija: {$p->category} | Dzimums: {$p->gender} | Cena: €{$p->price}"
        )->implode("\n");

        $systemPrompt = <<<PROMPT
Tu esi ARVA modes veikala personīgais stilists un iepirkšanās asistents. Tava misija ir palīdzēt klientam atrast ideālos apģērbus.

KATEGORIJU TULKOJUMI (izmanto lai saskaņotu klienta vārdus ar kataloga kategorijām):
- bikses / džinsi / jeans = kategorija "jeans"
- krekls / t-krekls / tshirt = kategorija "tshirt"
- jaka / žakete / jacket = kategorija "jacket"
- cepure / hat = kategorija "hat"
- apavi / kurpes / zābaki / shoes = kategorija "shoes"
- "vīriešu" = gender "men", "sieviešu" = gender "women"

KĀ STRĀDĀT:
1. Saproti ko klients vēlas — ja viņš saka "bikses", meklē tikai kategoriju "jeans"
2. Ieteic TIKAI produktus kas PRECĪZI atbilst pieprasītajai kategorijai
3. Ieteic 2-3 variantus ar nosaukumu un cenu
4. Paskaidro KĀPĒC šis produkts der
5. Atbildi latviski, draudzīgi, max 4 teikumi

PRODUKTU KATALOGS:
$catalog

KĀ NORISINĀS PASŪTĪJUMS UN APMAKSA (ja klients jautā kā pirkt / apmaksāt):
1. Klients spiež "Pievienot grozam" pie izvēlētā produkta
2. Atver grozu (ikona augšā) un spiež "Uz kasi"
3. Aizpilda piegādes adresi
4. Izvēlas apmaksas veidu: bankas karte (apmaksa notiek tieši lapā, droši ar Stripe) VAI apmaksa pie piegādes (skaidrā naudā kurjeram)
5. Apstiprina pasūtījumu — viss notiek vietnē, apmaksa NEKAD netiek veikta caur e-pastu
6. Pēc pasūtījuma apstiprinājuma kvīts tiek nosūtīta uz klienta e-pastu, taču tā ir tikai informatīva, nevis apmaksas veids

STINGRI NOTEIKUMI:
- Ja klients prasa bikses — ieteic TIKAI "jeans" kategorijas produktus, NEKAD apavus vai citu kategoriju
- Ja klients prasa kreklu — ieteic TIKAI "tshirt" kategorijas produktus
- Ieteic TIKAI produktus no kataloga ar precīzu nosaukumu un cenu
- Neizdomā produktus kas nav katalogā
- Neizdomā procesus, kontaktinformāciju vai apmaksas veidus, kas nav minēti šajā promptā — par apmaksu un pasūtījumiem atbildi STRIKTI saskaņā ar iepriekš aprakstīto procesu
- NEKAD neiesaki sūtīt naudu, apmaksu vai apmaksas apliecinājumu uz e-pastu — apmaksa notiek tikai vietnē (karte vai pie piegādes)
PROMPT;

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            array_map(fn($m) => ['role' => $m['role'] === 'assistant' ? 'assistant' : 'user', 'content' => $m['text']], $request->history ?? []),
            [['role' => 'user', 'content' => $request->message]]
        );

        $response = Http::timeout(15)
            ->withToken(env('GROQ_API_KEY'))
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'llama-3.1-8b-instant',
                'messages'    => $messages,
                'max_tokens'  => 300,
                'temperature' => 0.7,
            ]);

        if (!$response->successful()) {
            return response()->json(['reply' => 'Atvainojos, radās kļūda. Lūdzu mēģini vēlreiz.'], 200);
        }

        $reply = $response->json('choices.0.message.content') ?? 'Atvainojos, nesapratu. Lūdzu mēģini vēlreiz.';

        return response()->json(['reply' => $reply]);
    }
}
