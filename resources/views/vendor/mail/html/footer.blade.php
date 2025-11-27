@php
$foo = '© '. date('Y') .' Yod Invest. Tous droits réservés.'
@endphp
<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
{{ Illuminate\Mail\Markdown::parse($foo) }}
</td>
</tr>
</table>
</td>
</tr>
