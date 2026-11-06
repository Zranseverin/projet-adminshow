<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="color-scheme" content="light"><title>Bienvenue sur Zran Market</title>
<style>@media only screen and (max-width:600px){.email-outer{padding:20px 12px!important}.email-body{padding:30px 24px!important}.email-header{padding:24px!important}.email-title{font-size:27px!important}.email-button{display:block!important;text-align:center!important}}</style></head>
<body style="margin:0;padding:0;background-color:#f3f1ec;font-family:Arial,Helvetica,sans-serif;color:#243747;-webkit-text-size-adjust:100%">
    <div style="display:none;font-size:1px;color:#f3f1ec;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all">Bienvenue sur Zran Market. Validez votre adresse e-mail pour accéder à votre boutique.</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f1ec"><tr><td class="email-outer" align="center" style="padding:44px 20px">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px">
            <tr><td class="email-header" style="padding:28px 40px;background-color:#1c2b3a;border-radius:14px 14px 0 0">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr><td aria-hidden="true" style="width:42px;height:42px;background-color:#ff8a10;border-radius:10px;text-align:center;font-size:27px;font-weight:bold;color:#1c2b3a">Z</td><td style="padding-left:12px"><a href="{{ $brandUrl }}" style="font-size:23px;font-weight:bold;letter-spacing:-.5px;color:#ffffff;text-decoration:none">Zran Market</a><p style="margin:4px 0 0;font-size:11px;color:#d3dae1;letter-spacing:1px">VOTRE ESPACE VENDEUR</p></td></tr></table>
            </td></tr>
            <tr><td class="email-body" style="padding:40px;background-color:#ffffff;border-left:1px solid #e1e4e7;border-right:1px solid #e1e4e7">
                <p style="margin:0 0 16px;font-size:11px;line-height:1.5;letter-spacing:1.5px;font-weight:bold;color:#87571f">BIENVENUE DANS L’AVENTURE</p>
                <h1 class="email-title" style="margin:0 0 20px;font-size:32px;line-height:1.22;letter-spacing:-.8px;color:#1c2b3a">Votre boutique<br>commence ici.</h1>
                <p style="margin:0 0 12px;font-size:16px;line-height:1.7;color:#243747">Bonjour {{ $sellerName }},</p>
                <p style="margin:0 0 28px;font-size:15px;line-height:1.8;color:#526171">Merci de rejoindre Zran Market ! Il ne reste qu’une étape : confirmez votre adresse e-mail pour accéder à votre espace vendeur et préparer votre boutique.</p>
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;width:100%"><tr><td align="center" bgcolor="#1c2b3a" style="border-radius:7px;mso-padding-alt:16px 24px"><a class="email-button" href="{{ $verificationUrl }}" style="display:block;padding:17px 24px;border:1px solid #1c2b3a;border-radius:7px;color:#ffffff;font-size:15px;font-weight:bold;line-height:1.5;text-align:center;text-decoration:none">Valider mon adresse e-mail &nbsp; →</a></td></tr></table>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding:16px 18px;background-color:#faf5ec;border:1px solid #efe1cc;border-radius:7px"><p style="margin:0;font-size:13px;line-height:1.7;color:#6e512b"><strong>Un lien valable {{ $expiresIn }} minutes.</strong><br>Une fois ce délai passé, demandez un nouvel envoi depuis votre espace de connexion.</p></td></tr></table>
                <p style="margin:28px 0 0;font-size:14px;line-height:1.8;color:#526171">À très bientôt,<br><strong style="color:#1c2b3a">L’équipe Zran Market</strong></p>
            </td></tr>
            <tr><td style="padding:24px 40px;background-color:#fbfcfd;border:1px solid #e1e4e7;border-top:0;border-radius:0 0 14px 14px">
                <p style="margin:0 0 10px;font-size:12px;line-height:1.7;color:#596879">Le bouton ne fonctionne pas ? Copiez ce lien dans votre navigateur :</p>
                <p style="margin:0;font-size:11px;line-height:1.8;word-break:break-all;overflow-wrap:anywhere"><a href="{{ $verificationUrl }}" style="color:#315d7b;text-decoration:underline;word-break:break-all">{{ $verificationUrl }}</a></p>
            </td></tr>
            <tr><td align="center" style="padding:24px 16px"><p style="margin:0 0 10px;font-size:12px;line-height:1.8;color:#596879">Vous n’avez pas créé de compte ? Ignorez simplement ce message.<br>Aucune action n’est nécessaire.</p><p style="margin:0;font-size:11px;color:#596879">© {{ date('Y') }} Zran Market</p></td></tr>
        </table>
    </td></tr></table>
</body></html>
