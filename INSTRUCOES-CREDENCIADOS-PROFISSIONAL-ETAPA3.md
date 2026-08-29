# Credenciados profissional — Etapa 3

## Conteúdo

Esta entrega incremental acrescenta:

- filtro persistente por presídio no dashboard e integração com a listagem;
- alertas de credenciais vencidas e das que vencem entre hoje e o fim do mês;
- captação pública redesenhada para celular e computador, com modo escuro;
- validação hierárquica, normalização de CPF/telefones/CEP e validação real de CPF;
- arquivos limitados a imagens JPG/PNG/WebP de até 5 MB;
- gravação transacional, limpeza de arquivos em falha e mensagem pública sem detalhes internos;
- consentimento de privacidade;
- datas de primeira credencial, renovação opcional e validade na captação;
- aprovação compatível com as novas datas;
- busca da fila por nome, CPF ou telefone e revisão das datas por presídio;
- testes Feature/Livewire da etapa.

Não há migration nesta entrega.

## Aplicação local

Execute no WSL:

```bash
cd ~/dm

git status
git branch --show-current
```

A branch esperada é `credenciados-profissional-etapa2`. Preserve o arquivo não rastreado de instruções da etapa anterior.

Copie o pacote para Downloads e aplique:

```bash
cp /mnt/c/Users/moraws/Downloads/credenciados-profissional-etapa3.tar.gz /tmp/
tar -xzf /tmp/credenciados-profissional-etapa3.tar.gz -C ~/dm
cd ~/dm
```

Se o navegador baixar para o caminho Linux informado anteriormente, use:

```bash
cp /home/moraws/Downloads/credenciados-profissional-etapa3.tar.gz /tmp/
tar -xzf /tmp/credenciados-profissional-etapa3.tar.gz -C ~/dm
cd ~/dm
```

## Verificações

```bash
php artisan optimize:clear

php -l app/Livewire/Universal/CredenciadosDashboard.php
php -l app/Livewire/Universal/CaptacaoCredenciadoWizard.php
php -l app/Livewire/Universal/GestaoCaptacaoCredenciados.php
php -l tests/Feature/CaptacaoCredenciadoProfissionalTest.php
php -l tests/Feature/CredenciadosGestaoProfissionalTest.php

php artisan view:cache
php artisan view:clear

php artisan test --filter=CaptacaoCredenciadoProfissionalTest
php artisan test --filter='CredenciadosGestaoProfissionalTest|CredenciadosDashboardTest|CaptacaoCredenciadoProfissionalTest|PublicSecurityTest|TdaTeamAccessTest'

npm run build
```

Faça a revisão visual em:

- `http://127.0.0.1:8000/universal/universal/credenciados/dashboard`
- `http://127.0.0.1:8000/universal/universal/credenciados`
- `http://127.0.0.1:8000/captacao/credenciado`
- tela interna de gestão da captação já existente.

Teste em largura móvel e desktop, nos modos claro e escuro.

## Commit local, sem push

Somente depois de todos os testes passarem:

```bash
cd ~/dm
git status
git diff --check
git diff --stat

git add \
  app/Livewire/Universal/CredenciadosDashboard.php \
  app/Livewire/Universal/CaptacaoCredenciadoWizard.php \
  app/Livewire/Universal/GestaoCaptacaoCredenciados.php \
  resources/views/livewire/universal/credenciados-dashboard.blade.php \
  resources/views/livewire/universal/captacao-credenciado-wizard.blade.php \
  resources/views/livewire/universal/gestao-captacao-credenciados.blade.php \
  resources/views/components/captacao-input.blade.php \
  resources/views/components/captacao-select.blade.php \
  resources/views/components/captacao-textarea.blade.php \
  tests/Feature/CaptacaoCredenciadoProfissionalTest.php \
  tests/Feature/CredenciadosGestaoProfissionalTest.php

git commit -m "feat(credenciados): profissionaliza alertas e captacao"
git status
git log -1 --oneline
```

Não execute `git push` ainda. Não faça deploy e não execute migration em produção.

