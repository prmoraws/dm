# Handoff técnico — Curso UNP

## Objetivo

O módulo Curso UNP administra a inscrição pública, análise das captações,
formação de turmas, matrícula, chamada, resultado final e consulta central dos
alunos. Ele faz parte do projeto Laravel `dm` e utiliza Livewire, Tailwind CSS e
o controle de acesso já existente por equipe.

## Branch e ambiente

- Repositório: `prmoraws/dm`
- Branch de desenvolvimento e produção do módulo: `curso-unp-etapa1`
- Laravel 12
- PHP compatível com o projeto: 8.2 ou superior
- Área pública: `/curso-unp/inscricao`
- Área interna: prefixo `/unp/curso-unp`
- Middleware interno: autenticação, usuário verificado e `team.access:Unp`

Nunca versionar `.env`, credenciais, arquivos enviados pelos usuários, banco de
dados ou artefatos locais de build que estejam ignorados pelo Git.

## Fluxo funcional

1. O interessado realiza a inscrição pública e envia a foto.
2. A captação fica disponível para análise do gestor.
3. Ao aprovar, o gestor escolhe uma turma e o sistema cria a matrícula.
4. A turma passa a ser acompanhada por chamadas com presença, ausência ou
   justificativa.
5. O gestor registra manualmente o resultado final do aluno.
6. A página de alunos centraliza consulta, filtros, ficha, frequência e edição
   dos dados cadastrais.

## Rotas principais

| Nome | Caminho | Finalidade |
| --- | --- | --- |
| `curso-unp.inscricao` | `/curso-unp/inscricao` | Inscrição pública |
| `curso-unp.dashboard` | `/unp/curso-unp/dashboard` | Indicadores e atalhos |
| `curso-unp.captacoes` | `/unp/curso-unp/captacoes` | Análise das inscrições |
| `curso-unp.turmas` | `/unp/curso-unp/turmas` | Gestão das turmas |
| `curso-unp.alunos` | `/unp/curso-unp/alunos` | Lista e ficha dos alunos |
| `curso-unp.acompanhamento` | `/unp/curso-unp/acompanhamento` | Chamada e resultado final |

## Componentes Livewire

- `CaptacaoCursoUnpWizard`: inscrição pública em etapas.
- `CursoUnpDashboard`: resumo operacional.
- `GestaoCaptacoesCursoUnp`: aprovação ou rejeição e matrícula.
- `TurmasCursoUnp`: manutenção das turmas.
- `AlunosCursoUnp`: pesquisa, filtros, ficha, histórico e edição do aluno.
- `AcompanhamentoCursoUnp`: chamada e encerramento do curso.

As views correspondentes ficam em `resources/views/livewire/unp`.

## Modelo de dados

- `curso_unp_turmas`: período, local, instrutor, limite e situação da turma.
- `curso_unp_captacoes`: cadastro original e revisão da inscrição.
- `curso_unp_matriculas`: vínculo entre captação e turma, situação e resultado.
- `curso_unp_presencas`: uma ocorrência por matrícula e data de aula.

Relacionamentos importantes:

- Uma captação pode possuir matrículas.
- Uma turma possui matrículas.
- Uma matrícula pertence a uma captação e a uma turma.
- Uma matrícula possui registros de presença.

A restrição única `captacao_id + turma_id` impede matrícula duplicada na mesma
turma. A restrição `matricula_id + data_aula` impede duas chamadas para o mesmo
aluno na mesma data.

## Gestão de alunos

A página `/unp/curso-unp/alunos` lista somente pessoas que já possuem matrícula.
Ela permite:

- pesquisar por nome, celular ou igreja;
- filtrar por turma e situação da matrícula;
- consultar dados pessoais, turma e instrutor;
- consultar histórico e percentual de frequência;
- editar os dados cadastrais mantidos em `curso_unp_captacoes`.

A edição cadastral não transfere turma, não altera a situação da matrícula e não
reescreve presenças. Essas responsabilidades permanecem nos fluxos específicos
para preservar o histórico.

O percentual exibido é calculado por:

```text
presenças / total de chamadas registradas × 100
```

Faltas justificadas continuam identificadas separadamente e fazem parte do total
de chamadas.

## Regras de segurança e integridade

- A inscrição é pública; as demais páginas exigem acesso da equipe UNP ou Adm.
- A aprovação deve respeitar a capacidade da turma.
- Turmas com matrículas não devem ser excluídas.
- A data da chamada deve pertencer ao período da turma.
- Rejeições de captação exigem motivo.
- Telefones são normalizados para dígitos e verificados contra duplicidade na
  edição.
- Nunca editar matrícula ou presença ao salvar dados pessoais do aluno.

## Arquivos centrais

```text
app/Livewire/Unp/
app/Models/Unp/
config/curso_unp.php
database/migrations/2026_09_08_120000_create_curso_unp_tables.php
resources/views/livewire/unp/
tests/Feature/*CursoUnpTest.php
routes/web.php
```

## Validação local

Antes de cada publicação:

```bash
php artisan optimize:clear
php artisan test \
  tests/Feature/AlunosCursoUnpTest.php \
  tests/Feature/CursoUnpAcessoTest.php \
  tests/Feature/CursoUnpEstruturaTest.php \
  tests/Feature/GestaoCaptacoesCursoUnpTest.php \
  tests/Feature/TurmasCursoUnpTest.php \
  tests/Feature/AcompanhamentoCursoUnpTest.php
npm run build
git diff --check
```

Também validar manualmente inscrição, captações, turmas, alunos e chamada em
desktop e celular.

## Acesso e publicação segura

O repositório não possui workflow automático de deploy. A produção é atualizada
por SSH com a configuração mantida fora do Git:

```text
Alias: domo-production
Host: sv100.ifastnet.com
Usuário: domo
Porta: 1394
Chave local: ~/.ssh/domo_ifastnet_sync_rsa
Projeto remoto: /home/domo/public_html/domo.free.nf
Branch: curso-unp-etapa1
```

O alias equivale ao acesso explícito abaixo:

```bash
ssh -p 1394 -i ~/.ssh/domo_ifastnet_sync_rsa domo@sv100.ifastnet.com
```

Antes de publicar, confirmar por comandos somente leitura o diretório, branch,
commit, estado do Git, versão do PHP e espaço disponível. Interromper se houver
alterações remotas não identificadas.

Ordem recomendada:

1. Confirmar que a branch remota contém o commit aprovado.
2. Criar backup versionado em `/home/domo/deploy-backups`, preservando código,
   `.env`, banco de dados e uploads pertinentes à alteração.
3. Ativar o modo de manutenção quando a alteração exigir indisponibilidade.
4. Atualizar somente pela branch `curso-unp-etapa1` e por fast-forward.
5. Executar `composer install` somente quando o lockfile tiver mudado.
6. Executar migrations apenas depois de revisar `php artisan migrate --pretend`.
7. Executar `npm ci && npm run build` quando fontes ou dependências front-end
   exigirem build no servidor; alternativamente publicar o build validado.
8. Renovar os caches do Laravel.
9. Desativar o modo de manutenção e realizar o teste de fumaça.

Para a entrega da página de alunos não há migration nem nova dependência PHP ou
JavaScript.

Comandos Laravel usuais após a atualização:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Não executar esses comandos sem antes confirmar diretório, usuário e versão do
PHP do ambiente de produção.

## Teste de fumaça em produção

- autenticação continua funcionando;
- `/curso-unp/inscricao` continua público;
- `/unp/curso-unp/alunos` exige autenticação e abre para UNP/Adm;
- pesquisa e filtros retornam os alunos esperados;
- ficha mostra turma e presenças;
- edição cadastral salva sem alterar matrícula ou chamadas;
- páginas de captações, turmas e acompanhamento continuam abrindo;
- `storage/logs/laravel.log` não registra novos erros.

## Rollback

Se a publicação falhar:

1. colocar a aplicação em manutenção;
2. restaurar o commit anterior por um novo commit de reversão ou restaurar o
   backup do código;
3. restaurar banco e uploads somente se eles tiverem sido alterados;
4. executar `php artisan optimize:clear` e recriar os caches;
5. retirar a aplicação da manutenção e repetir o teste de fumaça.

Evitar `git reset --hard` em produção. Preferir atualização fast-forward,
`git revert` ou restauração do backup confirmado.

## Continuidade e evolução

Evoluções naturais do módulo:

- transferência de aluno entre turmas com histórico auditável;
- exportação CSV/PDF da lista e frequência;
- paginação e filtros adicionais para grandes volumes;
- registro de auditoria das alterações cadastrais;
- permissões mais granulares para leitura, chamada e edição;
- indicadores de evasão, conclusão e frequência por turma.

Qualquer evolução deve manter separadas as responsabilidades de cadastro,
matrícula e presença.
