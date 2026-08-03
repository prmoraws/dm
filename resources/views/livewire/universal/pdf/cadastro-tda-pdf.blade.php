<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<style>
@page{margin:30px 34px}*{box-sizing:border-box}body{margin:0;color:#111;font:11px DejaVu Sans,sans-serif}.page{height:770px;position:relative}.page-break{page-break-before:always}.head{height:58px;display:table;width:100%;margin-bottom:14px}.logo,.title{display:table-cell;vertical-align:middle}.logo{width:165px}.logo img{width:145px;height:auto}.title{background:#383838;color:#eee;text-align:center;font-size:18px;font-weight:bold;letter-spacing:.5px}.photo{position:absolute;right:0;top:0;width:110px;height:142px;border:1px solid #555;background:#eee;text-align:center;color:#aaa;font-weight:bold;overflow:hidden}.photo img{width:110px;height:142px;object-fit:cover}.photo span{display:block;margin-top:62px}.top{padding-right:125px}.row{display:table;width:100%;margin:7px 0}.cell{display:table-cell;padding-right:8px;vertical-align:bottom}.w20{width:20%}.w25{width:25%}.w33{width:33.333%}.w40{width:40%}.w50{width:50%}.w60{width:60%}.field{display:inline-block;background:#eee;border-bottom:1px solid #bbb;min-height:24px;padding:6px 7px;vertical-align:middle;width:100%;font-weight:bold}.label{white-space:nowrap;padding-right:5px}.line{border-top:1px solid #444;margin:18px 0 12px}.section{font-size:13px;font-weight:bold;margin:12px 0 8px;text-transform:uppercase}.question{margin:11px 0}.box{display:inline-block;width:15px;height:15px;border:1px solid #999;border-radius:50%;text-align:center;line-height:14px;margin:0 4px 0 9px;font-size:10px}.checked{font-weight:bold;color:#000}.days{margin:8px 0 14px}.privacy{position:absolute;bottom:0;left:0;right:0;font-size:6.5px;line-height:1.2;text-align:justify;padding-right:82px}.privacy img{position:absolute;right:0;bottom:0;width:70px;height:70px}.version{position:absolute;right:-18px;bottom:2px;font-size:6px;transform:rotate(90deg)}
</style>
</head>
<body>
@php
$f=fn($d)=>$d?\Carbon\Carbon::parse($d)->format('d/m/Y'):'';
$yes=fn($v)=>(bool)$v;
$dias=['segunda'=>'SEG.','terca'=>'TER.','quarta'=>'QUA.','quinta'=>'QUI.','sexta'=>'SEX.','sabado'=>'SÁB.','domingo'=>'DOM.'];
$check=fn($ok)=>$ok?'X':'';
@endphp
<div class="page">
  <div class="head"><div class="logo"><img src="{{ $logoDataUri }}"></div><div class="title">FICHA DE CADASTRO</div></div>
  <div class="photo">@if($fotoDataUri)<img src="{{ $fotoDataUri }}">@else<span>FOTO</span>@endif</div>
  <div class="top">
    <div class="row"><div class="cell w50"><span class="label">Igreja:</span><span class="field">{{ $pessoa->igreja->nome??'' }}</span></div><div class="cell w50"><span class="label">Bloco:</span><span class="field">{{ $pessoa->bloco->nome??'' }}</span></div></div>
    <div class="row"><div class="cell w50"><span class="label">Região:</span><span class="field">{{ $pessoa->regiao->nome??'' }}</span></div><div class="cell w50"><span class="label">Estado:</span><span class="field">{{ $pessoa->estado->nome??'' }}</span></div></div>
    <div class="row"><div class="cell w50"><span class="label">Data em que ingressou no grupo:</span><span class="field">{{ $f($pessoa->data_ingresso_grupo) }}</span></div></div>
    <div class="row"><div class="cell"><span class="label">Função no grupo:</span><span class="field">{{ $pessoa->funcao_grupo }}</span></div></div>
  </div>
  <div class="line"></div><div class="section">Dados pessoais</div>
  <div class="row"><div class="cell"><span class="label">Nome:</span><span class="field">{{ $pessoa->nome }}</span></div></div>
  <div class="row"><div class="cell w50"><span class="label">Data de nascimento:</span><span class="field">{{ $f($pessoa->data_nascimento) }}</span></div><div class="cell w50"><span class="label">Estado civil:</span><span class="field">{{ $pessoa->estado_civil }}</span></div></div>
  <div class="row"><div class="cell w50"><span class="label">RG/CIN n.º:</span><span class="field">{{ $pessoa->rg }}</span></div><div class="cell w50"><span class="label">CPF n.º:</span><span class="field">{{ $pessoa->cpf }}</span></div></div>
  <div class="row"><div class="cell w50"><span class="label">Celular/WhatsApp:</span><span class="field">{{ $pessoa->celular }}</span></div></div>
  <div class="row"><div class="cell w50"><span class="label">Facebook:</span><span class="field">{{ $pessoa->facebook }}</span></div><div class="cell w50"><span class="label">Instagram:</span><span class="field">{{ $pessoa->instagram }}</span></div></div>
  <div class="row"><div class="cell w60"><span class="label">Endereço:</span><span class="field">{{ $pessoa->endereco }}</span></div><div class="cell w20"><span class="label">N.º:</span><span class="field">{{ $pessoa->numero }}</span></div></div>
  <div class="row"><div class="cell w20"><span class="label">CEP:</span><span class="field">{{ $pessoa->cep }}</span></div><div class="cell w25"><span class="label">Bairro:</span><span class="field">{{ $pessoa->bairro }}</span></div><div class="cell w40"><span class="label">Cidade:</span><span class="field">{{ $pessoa->cidade->nome??'' }}</span></div><div class="cell"><span class="label">UF:</span><span class="field">{{ $pessoa->estado->uf??'' }}</span></div></div>
  <div class="row"><div class="cell"><span class="label">E-mail:</span><span class="field">{{ $pessoa->email }}</span></div></div>
  <div class="row"><div class="cell w50"><span class="label">Grau de escolaridade:</span><span class="field">{{ $pessoa->escolaridade }}</span></div><div class="cell w50"><span class="label">Profissão:</span><span class="field">{{ $pessoa->profissao }}</span></div></div>
  <div class="question">Tem filhos? <i class="box">{{ $check($yes($pessoa->tem_filhos)) }}</i> SIM <i class="box">{{ $check(!$yes($pessoa->tem_filhos)) }}</i> NÃO &nbsp; Se sim, quantos? <span class="field" style="width:70px">{{ $pessoa->quantidade_filhos }}</span> &nbsp; Idade dos filhos: <span class="field" style="width:145px">{{ $pessoa->idade_filhos }}</span></div>
  <div class="section" style="text-transform:none">Em caso de emergência, contatar:</div>
  <div class="row"><div class="cell"><span class="label">Nome:</span><span class="field">{{ $pessoa->emergencia_nome }}</span></div></div>
  <div class="row"><div class="cell w50"><span class="label">Celular/WhatsApp:</span><span class="field">{{ $pessoa->emergencia_celular }}</span></div></div>
  <div class="row"><div class="cell w50"><span class="label">Facebook:</span><span class="field">{{ $pessoa->emergencia_facebook }}</span></div><div class="cell w50"><span class="label">Instagram:</span><span class="field">{{ $pessoa->emergencia_instagram }}</span></div></div>
  <div class="privacy"><b>PRIVACIDADE DE DADOS:</b> Ao preencher esta ficha, você confirma seu consentimento para o cadastro como voluntário do grupo <b>Auxiliares da Terapia do Amor da Igreja Universal do Reino de Deus</b>, com base nos artigos 7.º e 11.º da LGPD. Este cadastro tem como finalidade gerenciar os voluntários do grupo e possibilitar contatos sobre as atividades realizadas. Para mais informações: <b>privacidade@universal.org</b> e <b>https://www.universal.org/politica-de-privacidade/</b>. @if($qrDataUri)<img src="{{ $qrDataUri }}">@endif</div><div class="version">V.2209/25</div>
</div>

<div class="page page-break">
  <div class="head"><div class="logo"><img src="{{ $logoDataUri }}"></div><div class="title">FICHA DE CADASTRO</div></div>
  <div class="section">Dados espirituais</div>
  <div class="question">@foreach(['membro'=>'Membro','cpo'=>'CPO','colaborador'=>'Colaborador','obreiro'=>'Obreiro','levita'=>'Levita','auxiliar'=>'Auxiliar'] as $k=>$v)<i class="box">{{ $check($pessoa->condicao_atual===$k) }}</i>{{ $v }} @endforeach</div>
  <div class="question">Início na IURD: <span class="field" style="width:135px">{{ $f($pessoa->inicio_iurd) }}</span></div>
  <div class="question">Batizado nas águas na IURD? <i class="box">{{ $check($yes($pessoa->batizado_aguas)) }}</i> SIM <i class="box">{{ $check(!$yes($pessoa->batizado_aguas)) }}</i> NÃO &nbsp; Se sim, data: <span class="field" style="width:135px">{{ $f($pessoa->data_batismo_aguas) }}</span></div>
  <div class="question">Batizado com o Espírito Santo? <i class="box">{{ $check($yes($pessoa->batizado_espirito_santo)) }}</i> SIM <i class="box">{{ $check(!$yes($pessoa->batizado_espirito_santo)) }}</i> NÃO &nbsp; Se sim, data: <span class="field" style="width:135px">{{ $f($pessoa->data_batismo_espirito_santo) }}</span></div>
  <div class="question">Já se afastou? <i class="box">{{ $check($yes($pessoa->ja_se_afastou)) }}</i> SIM <i class="box">{{ $check(!$yes($pessoa->ja_se_afastou)) }}</i> NÃO</div>
  <div class="question">Em quais dias você participa das reuniões da igreja?</div><div class="days">@foreach($dias as $k=>$v)<i class="box">{{ $check(in_array($k,$pessoa->dias_reunioes??[])) }}</i>{{ $v }} @endforeach</div>
  <div class="question">Em quais dias você evangeliza?</div><div class="days">@foreach($dias as $k=>$v)<i class="box">{{ $check(in_array($k,$pessoa->dias_evangelizacao??[])) }}</i>{{ $v }} @endforeach</div>
  <div class="line"></div><div class="section" style="text-transform:none">Mulher:</div>
  <div class="question">Participa das reuniões do Godllywood Autoajuda? <i class="box">{{ $check($yes($pessoa->godllywood_autoajuda)) }}</i> SIM <i class="box">{{ $check(!$yes($pessoa->godllywood_autoajuda)) }}</i> NÃO</div>
  <div class="question">Assiste à meditação da Palavra no Univer? <i class="box">{{ $check($yes($pessoa->meditacao_univer)) }}</i> SIM <i class="box">{{ $check(!$yes($pessoa->meditacao_univer)) }}</i> NÃO</div>
  <div class="section" style="text-transform:none">Homem:</div>
  <div class="question">Participa das reuniões do IntelliMen? <i class="box">{{ $check($yes($pessoa->intellimen_reunioes)) }}</i> SIM <i class="box">{{ $check(!$yes($pessoa->intellimen_reunioes)) }}</i> NÃO</div>
  <div class="question">Já fez ou faz os desafios do IntelliMen? <i class="box">{{ $check($yes($pessoa->intellimen_desafios)) }}</i> SIM <i class="box">{{ $check(!$yes($pessoa->intellimen_desafios)) }}</i> NÃO</div>
  <div class="line"></div><div class="section">Para colaborador/obreiro/levita</div>
  @foreach(['colaborador'=>'Colaborador','obreiro'=>'Obreiro','levita'=>'Levita'] as $k=>$v)<div class="question"><i class="box">{{ $check($yes($pessoa->{$k})) }}</i> {{ $v }} &nbsp;&nbsp; Data da graduação: <span class="field" style="width:140px">{{ $f($pessoa->{'data_graduacao_'.$k}) }}</span></div>@endforeach
  <div class="question">Em quais dias você trabalha na reunião?</div><div class="days">@foreach($dias as $k=>$v)<i class="box">{{ $check(in_array($k,$pessoa->dias_trabalho_reuniao??[])) }}</i>{{ $v }} @endforeach</div>
  <div class="privacy"><b>PRIVACIDADE DE DADOS:</b> Ao preencher esta ficha, você confirma seu consentimento para o cadastro como voluntário do grupo <b>Auxiliares da Terapia do Amor da Igreja Universal do Reino de Deus</b>, com base nos artigos 7.º e 11.º da LGPD. Seus dados são tratados conforme a Política de Privacidade da Universal. Para dúvidas: <b>privacidade@universal.org</b>. @if($qrDataUri)<img src="{{ $qrDataUri }}">@endif</div><div class="version">V.2209/25</div>
</div>
</body></html>
