# LexPraxis IA — Backlog Completo
> Sistema de Gestão para Escritórios de Advocacia

---

## Legenda
- 🔴 Crítico / Alta prioridade
- 🟡 Médio prazo
- 🟢 Melhoria / Nice to have
- ✅ Concluído
- 🚧 Em desenvolvimento

---

## ✅ Concluído

- [x] Login de usuários
- [x] Registro de usuários
- [x] Chat com IA (OpenAI GPT-4o)
- [x] Consulta de processos via DataJud (todos os tribunais brasileiros)
- [x] Histórico de consultas
- [x] Controle de acessos por usuário (saldo de consultas)
- [x] Interface dark mode com Bootswatch Darkly
- [x] Redirecionamento na raiz (`index.php`)
- [x] Ambiente local XAMPP configurado

---

## 🔴 FASE 1 — Base do CRM (Fundação)

### 1.1 Dashboard Principal
- [ ] Card: total de clientes ativos
- [ ] Card: processos em andamento
- [ ] Card: audiências nos próximos 7 dias
- [ ] Card: tarefas em atraso
- [ ] Card: honorários a receber no mês
- [ ] Lista: próximas 5 audiências
- [ ] Lista: prazos críticos (vencendo em até 3 dias)
- [ ] Lista: tarefas pendentes do dia
- [ ] Gráfico: processos por área do direito
- [ ] Gráfico: receita mensal dos últimos 6 meses
- [ ] Notificações em tempo real (badge no sino)

### 1.2 Gestão de Clientes
- [ ] Cadastro de cliente pessoa física (nome, CPF, RG, data nascimento, estado civil, profissão)
- [ ] Cadastro de cliente pessoa jurídica (razão social, CNPJ, inscrição estadual, representante legal)
- [ ] Campos de contato: telefone, celular, e-mail, WhatsApp
- [ ] Endereço completo com busca por CEP (ViaCEP)
- [ ] Campo de observações internas (visível só ao advogado)
- [ ] Status do cliente: ativo, inativo, prospecto
- [ ] Foto/avatar do cliente
- [ ] Listagem com busca por nome, CPF/CNPJ, telefone
- [ ] Filtros por status, data de cadastro, responsável
- [ ] Paginação na listagem
- [ ] Perfil completo do cliente com aba de processos vinculados
- [ ] Histórico de atendimentos do cliente
- [ ] Validação de CPF e CNPJ (algoritmo)
- [ ] Consulta automática Receita Federal via CNPJ (PJ)
- [ ] Exportar lista de clientes (CSV / PDF)
- [ ] Soft delete (arquivar sem excluir)

### 1.3 Gestão de Processos
- [ ] Cadastro de processo vinculado a cliente
- [ ] Número CNJ com validação de formato
- [ ] Tipo de ação (cível, criminal, trabalhista, previdenciário, tributário, família, etc.)
- [ ] Área do direito (dropdown configurável)
- [ ] Status: em andamento, suspenso, arquivado, encerrado, ganho, perdido
- [ ] Vara, comarca e tribunal
- [ ] Juiz responsável
- [ ] Partes contrárias (nome, CPF/CNPJ, advogado da parte contrária)
- [ ] Polo: ativo, passivo, terceiro interessado
- [ ] Valor da causa
- [ ] Data de distribuição
- [ ] Advogado responsável pelo processo
- [ ] Integração automática com DataJud (puxar dados ao cadastrar número CNJ)
- [ ] Atualização automática de movimentações via DataJud (agendado)
- [ ] Timeline de movimentações do processo
- [ ] Aba de documentos do processo
- [ ] Aba de prazos do processo
- [ ] Aba de honorários do processo
- [ ] Aba de tarefas do processo
- [ ] Aba de notas internas
- [ ] Listagem de processos com busca e filtros avançados
- [ ] Filtros: área, status, advogado responsável, período
- [ ] Exportar lista de processos (CSV / PDF)
- [ ] Vincular múltiplos clientes a um processo (litisconsórcio)
- [ ] Histórico de alterações do processo (audit trail)

---

## 🔴 FASE 2 — Agenda, Prazos e Tarefas

### 2.1 Agenda / Calendário
- [ ] Visualização mensal, semanal e diária
- [ ] Tipos de evento: audiência, reunião, prazo, despacho, perícia, conciliação
- [ ] Cor por tipo de evento
- [ ] Vinculação de evento a processo e/ou cliente
- [ ] Campo de local (presencial ou virtual com link)
- [ ] Campo de observações
- [ ] Evento recorrente (semanal, mensal)
- [ ] Notificação por e-mail (24h antes, 3h antes)
- [ ] Notificação via WhatsApp (via Z-API ou Evolution API)
- [ ] Notificação push no browser
- [ ] Integração com Google Calendar (exportar/importar .ics)
- [ ] Visualização por advogado (multi-usuário)
- [ ] Mini calendário na sidebar do dashboard
- [ ] Drag & drop para reagendar eventos
- [ ] Exportar agenda do período (PDF)

### 2.2 Controle de Prazos
- [ ] Cadastro de prazo vinculado a processo
- [ ] Tipos: prazo fatal, prazo útil, prazo em dobro (MP/Defensoria), intimação
- [ ] Data da intimação + cálculo automático de prazo (dias corridos ou úteis)
- [ ] Exclusão de feriados nacionais e estaduais no cálculo
- [ ] Status: pendente, em andamento, cumprido, vencido, suspenso
- [ ] Prazo automático criado ao detectar movimentação no DataJud
- [ ] Alertas escalonados: 5 dias, 3 dias, 1 dia, no dia
- [ ] Painel de prazos por urgência (crítico, atenção, ok)
- [ ] Prazos vencidos destacados em vermelho
- [ ] Relatório de prazos por período e advogado
- [ ] API de feriados nacionais e estaduais integrada

### 2.3 Gestão de Tarefas
- [ ] Criar tarefa vinculada a processo, cliente ou avulsa
- [ ] Título, descrição, prioridade (alta, média, baixa)
- [ ] Responsável (advogado ou estagiário)
- [ ] Data de vencimento
- [ ] Status: a fazer, em andamento, concluída, cancelada
- [ ] Checklist dentro da tarefa (subtarefas)
- [ ] Comentários na tarefa
- [ ] Notificação ao ser atribuído a uma tarefa
- [ ] Quadro Kanban (arrastar entre colunas)
- [ ] Listagem por responsável, processo, prazo
- [ ] Tarefas recorrentes
- [ ] Relatório de produtividade por advogado

---

## 🔴 FASE 3 — Financeiro

### 3.1 Contrato de Honorários
- [ ] Tipos de honorário: fixo, mensal, por êxito, misto
- [ ] Valor acordado, percentual de êxito
- [ ] Forma de pagamento: à vista, parcelado, recorrente
- [ ] Data de início e encerramento
- [ ] Status: ativo, quitado, inadimplente, cancelado
- [ ] Vinculado a processo e cliente
- [ ] Histórico de alterações do contrato
- [ ] Geração de contrato de honorários em PDF (com template editável)
- [ ] Assinatura digital via DocuSign / D4Sign / Clicksign

### 3.2 Controle de Pagamentos
- [ ] Registro de parcelas a receber
- [ ] Marcar parcela como paga (com data e valor real)
- [ ] Calcular juros e multa por atraso
- [ ] Emissão de recibo em PDF
- [ ] Alertas de vencimento (3 dias antes)
- [ ] Alerta de inadimplência
- [ ] Filtro: pagamentos em aberto, vencidos, recebidos

### 3.3 Controle de Despesas
- [ ] Registro de despesa por processo (custas, peritos, diligências, deslocamento)
- [ ] Categoria da despesa (dropdown configurável)
- [ ] Reembolsável pelo cliente: sim/não
- [ ] Upload de comprovante (nota fiscal, recibo)
- [ ] Resumo de despesas por processo

### 3.4 Dashboard Financeiro
- [ ] Receita realizada no mês (honorários pagos)
- [ ] Receita prevista no mês (parcelas a vencer)
- [ ] Inadimplência total
- [ ] Despesas do mês
- [ ] Lucro líquido estimado
- [ ] Gráfico de receita x despesa por mês (12 meses)
- [ ] Ranking de clientes por receita gerada
- [ ] Projeção de recebimentos (próximos 30/60/90 dias)

### 3.5 Relatórios Financeiros
- [ ] Relatório de honorários por período
- [ ] Relatório de inadimplência
- [ ] Relatório de despesas por categoria
- [ ] Fluxo de caixa
- [ ] Exportar relatórios em PDF e Excel

---

## 🟡 FASE 4 — Documentos

### 4.1 Gestão de Documentos
- [ ] Upload de documentos por processo (PDF, DOCX, XLSX, imagens)
- [ ] Categorias: petição inicial, contestação, recurso, contrato, procuração, comprovante, outros
- [ ] Versões de documentos (v1, v2, v3...)
- [ ] Busca por nome de arquivo e tipo
- [ ] Visualização de PDF no browser (sem download)
- [ ] Limite de tamanho por arquivo (configurável)
- [ ] Quota de armazenamento por escritório
- [ ] Compartilhar documento com cliente via link temporário
- [ ] Soft delete com lixeira

### 4.2 Modelos de Documentos (Templates)
- [ ] Biblioteca de modelos editáveis (DOCX)
- [ ] Campos dinâmicos: {{nome_cliente}}, {{cpf}}, {{numero_processo}}, {{data}}, {{advogado}}, etc.
- [ ] Modelos incluídos no sistema:
  - [ ] Procuração Ad Judicia
  - [ ] Contrato de Honorários
  - [ ] Declaração de Hipossuficiência
  - [ ] Notificação Extrajudicial
  - [ ] Substabelecimento
  - [ ] Recibo de Honorários
- [ ] Editor de templates no browser (TinyMCE ou Quill)
- [ ] Geração de documento preenchido em PDF e DOCX
- [ ] Modelos personalizados pelo escritório

### 4.3 Assinatura Digital
- [ ] Envio de documento para assinatura por e-mail
- [ ] Integração com D4Sign (API brasileira)
- [ ] Status da assinatura: aguardando, assinado, recusado
- [ ] Armazenamento do documento assinado vinculado ao processo

---

## 🟡 FASE 5 — IA Avançada

### 5.1 Aprimoramentos no Chat Atual
- [ ] Contexto persistente entre sessões (salvo no banco)
- [ ] Resumo automático de processo ao cadastrar número CNJ
- [ ] Sugestão de próximos passos processuais pela IA
- [ ] Identificação de teses jurídicas aplicáveis ao caso
- [ ] Pesquisa de jurisprudência (STJ, STF, TST) por tema
- [ ] Cálculo de prescrição e decadência

### 5.2 Análise de Documentos
- [ ] Upload de peça/contrato para análise pela IA
- [ ] Resumo automático de documentos longos
- [ ] Identificação de cláusulas abusivas em contratos
- [ ] Extração automática de dados (partes, valores, datas) de petições
- [ ] Sugestão de correções e melhorias em peças

### 5.3 Geração de Peças com IA
- [ ] Geração de petição inicial a partir dos dados do processo
- [ ] Geração de contestação com base nos dados do processo contrário
- [ ] Geração de recurso de apelação
- [ ] Geração de notificação extrajudicial
- [ ] Geração de parecer jurídico
- [ ] Revisão ortográfica e jurídica de peças
- [ ] Formatação automática no padrão ABNT

### 5.4 Monitoramento Automático
- [ ] Monitorar processos cadastrados e alertar ao detectar nova movimentação
- [ ] Scraping do Diário Oficial para intimações
- [ ] Monitoramento de prazos com alertas automáticos por IA
- [ ] Relatório semanal automático por e-mail com resumo dos processos ativos

---

## 🟡 FASE 6 — Portal do Cliente

### 6.1 Acesso do Cliente
- [ ] Login separado para clientes (acesso limitado)
- [ ] Visualização dos próprios processos e status
- [ ] Visualização de documentos compartilhados pelo advogado
- [ ] Download de documentos autorizados
- [ ] Histórico de pagamentos e boletos
- [ ] Solicitação de reunião (agenda online)
- [ ] Chat direto com o advogado responsável
- [ ] Notificações de novas movimentações nos processos

### 6.2 Comunicação
- [ ] Chat interno advogado ↔ cliente
- [ ] Chat interno entre membros do escritório
- [ ] Envio de documentos via chat
- [ ] Histórico de conversas arquivado

---

## 🟡 FASE 7 — Multi-usuário e Escritório

### 7.1 Gestão de Equipe
- [ ] Cadastro de múltiplos usuários por escritório
- [ ] Perfis: sócio, advogado, estagiário, secretária, financeiro, admin
- [ ] Permissões granulares por perfil (ver/criar/editar/deletar por módulo)
- [ ] Advogado só vê seus próprios processos (ou todos, conforme permissão)
- [ ] Registro de atividades por usuário (audit log)
- [ ] Login multi-escritório (um usuário em vários escritórios)

### 7.2 Configurações do Escritório
- [ ] Nome, CNPJ, OAB, endereço, telefone, logo
- [ ] Personalização do sistema com logo e cores do escritório
- [ ] Configurar áreas do direito que o escritório atua
- [ ] Configurar tipos de despesa personalizados
- [ ] Configurar feriados estaduais específicos da comarca
- [ ] Assinatura padrão de e-mails
- [ ] Dados para rodapé de documentos gerados

### 7.3 Planos e Assinaturas (SaaS)
- [ ] Plano Starter: 1 usuário, 50 clientes, 100 processos
- [ ] Plano Profissional: 3 usuários, ilimitado
- [ ] Plano Escritório: usuários ilimitados + portal do cliente + IA avançada
- [ ] Cobrança recorrente (Stripe ou Mercado Pago)
- [ ] Período de teste gratuito (14 dias)
- [ ] Bloqueio automático ao vencer plano
- [ ] Painel administrativo do sistema (super admin)

---

## 🟢 FASE 8 — Integrações Externas

### 8.1 Tribunais e Órgãos
- [ ] ✅ DataJud — CNJ (todos os tribunais)
- [ ] Consulta Diário Oficial da União (DOU)
- [ ] Diário da Justiça Eletrônico (DJE) por estado
- [ ] Consulta CPF (Receita Federal — via convênio)
- [ ] Consulta CNPJ (Receita Federal — API pública)
- [ ] Validação OAB (Conselho Federal)
- [ ] Tabela de feriados nacionais e estaduais (API ANBIMA ou manual)
- [ ] Consulta BACENJUD / SISBAJUD (bloqueio de bens)
- [ ] Consulta Infojud (declaração de bens IR)

### 8.2 Comunicação
- [ ] E-mail transacional (SendGrid / Amazon SES)
- [ ] WhatsApp Business (Z-API / Evolution API / Twilio)
  - [ ] Notificação de prazo
  - [ ] Notificação de audiência
  - [ ] Envio de documentos
  - [ ] Chatbot de atendimento inicial
- [ ] SMS de alerta para prazos críticos
- [ ] Push notification no browser (PWA)

### 8.3 Financeiro
- [ ] Mercado Pago (geração de boleto / Pix / link de pagamento)
- [ ] Stripe (para planos SaaS)
- [ ] Nota fiscal eletrônica (NF-e) via API (eNotas / Plugnotas)
- [ ] Integração com contabilidade (exportar para Omie / Conta Azul)

### 8.4 Produtividade
- [ ] Google Calendar (sync bidirecional)
- [ ] Microsoft Outlook Calendar
- [ ] Google Drive / OneDrive (armazenamento de documentos)
- [ ] Dropbox
- [ ] Zapier / Make (automações personalizadas)

### 8.5 IA e NLP
- [ ] ✅ OpenAI GPT-4o
- [ ] Anthropic Claude (alternativa/backup)
- [ ] Google Gemini (alternativa)
- [ ] Whisper API (transcrição de audiências em áudio)
- [ ] Busca semântica em documentos (embeddings + pgvector)

---

## 🟢 FASE 9 — Mobile e PWA

- [ ] PWA (Progressive Web App) — instalável no celular
- [ ] Manifest.json e service worker
- [ ] Notificações push no mobile
- [ ] Offline mode para consulta de dados básicos
- [ ] App nativo Android (React Native ou Flutter) — fase futura
- [ ] App nativo iOS — fase futura
- [ ] Biometria para login no app

---

## 🟢 FASE 10 — Segurança e LGPD

### 10.1 Segurança
- [ ] Autenticação em dois fatores (2FA) via TOTP (Google Authenticator)
- [ ] Login social (Google OAuth)
- [ ] Bloqueio de conta após tentativas falhas (brute force)
- [ ] Sessão com expiração automática por inatividade
- [ ] HTTPS obrigatório (Let's Encrypt)
- [ ] Rate limiting nas APIs
- [ ] Senhas com requisitos mínimos de complexidade
- [ ] Log de acessos e ações (audit trail completo)
- [ ] Alerta de login de IP desconhecido

### 10.2 LGPD — Lei Geral de Proteção de Dados
- [ ] Política de privacidade e termos de uso
- [ ] Consentimento explícito no cadastro
- [ ] Direito ao esquecimento (exclusão completa de dados do cliente)
- [ ] Exportação dos dados do usuário (portabilidade)
- [ ] Anonimização de dados em relatórios
- [ ] DPO (Encarregado de Dados) configurável
- [ ] Log de operações com dados pessoais
- [ ] Criptografia de dados sensíveis no banco (CPF, RG)

### 10.3 Backup e Disponibilidade
- [ ] Backup automático diário do banco de dados
- [ ] Backup dos arquivos enviados
- [ ] Retenção de 30 dias de backups
- [ ] Restauração de backup com 1 clique (admin)
- [ ] Monitoramento de uptime (UptimeRobot ou similar)
- [ ] Alertas de erro em produção (Sentry)

---

## 🟢 FASE 11 — Relatórios e BI

- [ ] Relatório de produtividade por advogado (processos, tarefas concluídas, horas)
- [ ] Relatório de processos por área do direito
- [ ] Relatório de taxa de êxito (ganhos x perdidos)
- [ ] Relatório de clientes captados por período
- [ ] Relatório de inadimplência detalhado
- [ ] Dashboard analítico com gráficos interativos (Chart.js / Recharts)
- [ ] Exportar todos os relatórios em PDF e Excel
- [ ] Envio automático de relatório mensal por e-mail (para sócios)
- [ ] Comparativo mês a mês

---

## 🟢 FASE 12 — UX e Interface

- [ ] Tema claro / escuro (toggle)
- [ ] Layout responsivo completo (mobile first)
- [ ] Sidebar colapsável
- [ ] Breadcrumbs em todas as páginas
- [ ] Busca global (processos, clientes, documentos) com atalho `/`
- [ ] Atalhos de teclado (criar processo, criar cliente, abrir agenda)
- [ ] Tour guiado para novos usuários (onboarding)
- [ ] Tooltips explicativos
- [ ] Página de ajuda / FAQ
- [ ] Suporte via chat (Crisp / Intercom)
- [ ] Feedback visual em todas as ações (toast notifications)
- [ ] Skeleton loading (carregamento elegante)
- [ ] Modo de alto contraste (acessibilidade)
- [ ] Fonte configurável (tamanho)

---

## 📋 Resumo por Fase

| Fase | Descrição | Prioridade | Status |
|------|-----------|------------|--------|
| 1 | Dashboard + Clientes + Processos | 🔴 Alta | 🚧 Próxima |
| 2 | Agenda + Prazos + Tarefas | 🔴 Alta | Pendente |
| 3 | Financeiro | 🔴 Alta | Pendente |
| 4 | Documentos + Templates | 🟡 Média | Pendente |
| 5 | IA Avançada | 🟡 Média | Pendente |
| 6 | Portal do Cliente | 🟡 Média | Pendente |
| 7 | Multi-usuário + SaaS | 🟡 Média | Pendente |
| 8 | Integrações Externas | 🟢 Baixa | Pendente |
| 9 | Mobile / PWA | 🟢 Baixa | Pendente |
| 10 | Segurança + LGPD | 🟢 Baixa | Pendente |
| 11 | Relatórios + BI | 🟢 Baixa | Pendente |
| 12 | UX / Interface | 🟢 Contínuo | Pendente |

---

## 🗄️ Banco de Dados — Novas Tabelas

```sql
-- Escritório / Tenants
escritorios         (id, nome, cnpj, oab, telefone, email, logo, plano, ativo)

-- Equipe
usuarios_escritorio (id, usuario_id, escritorio_id, perfil, ativo)

-- Clientes
clientes            (id, escritorio_id, tipo, nome, cpf_cnpj, rg, nascimento,
                     estado_civil, profissao, email, telefone, celular, whatsapp,
                     cep, logradouro, numero, complemento, bairro, cidade, uf,
                     status, observacoes, criado_por, created_at)

-- Processos
processos           (id, escritorio_id, cliente_id, advogado_id, numero_cnj,
                     tipo_acao, area_direito, status, polo, vara, comarca,
                     tribunal, juiz, partes_contrarias, valor_causa,
                     data_distribuicao, observacoes, created_at)

-- Partes do Processo
processo_partes     (id, processo_id, nome, cpf_cnpj, tipo, advogado_contrario)

-- Movimentações (cache DataJud)
movimentacoes       (id, processo_id, data_hora, descricao, complemento,
                     codigo_datajud, sincronizado_em)

-- Agenda
agenda              (id, escritorio_id, usuario_id, processo_id, cliente_id,
                     titulo, tipo, data_inicio, data_fim, local, link_virtual,
                     descricao, recorrente, status, created_at)

-- Prazos
prazos              (id, escritorio_id, processo_id, usuario_id, descricao,
                     data_intimacao, data_prazo, tipo_prazo, status,
                     cumprido_em, observacoes)

-- Tarefas
tarefas             (id, escritorio_id, processo_id, cliente_id, criado_por,
                     responsavel_id, titulo, descricao, prioridade, status,
                     prazo, concluida_em)

-- Subtarefas
tarefas_itens       (id, tarefa_id, descricao, concluido)

-- Honorários
honorarios          (id, escritorio_id, processo_id, cliente_id, tipo,
                     valor_fixo, percentual_exito, forma_pagamento,
                     dia_vencimento, status, data_inicio, data_fim)

-- Parcelas de honorários
parcelas            (id, honorario_id, numero, valor, vencimento,
                     pago, data_pagamento, valor_pago, observacao)

-- Despesas
despesas            (id, escritorio_id, processo_id, usuario_id, descricao,
                     categoria, valor, data, reembolsavel, comprovante, created_at)

-- Documentos
documentos          (id, escritorio_id, processo_id, usuario_id, nome,
                     categoria, caminho, tamanho, tipo_mime, versao, created_at)

-- Templates de documentos
doc_templates       (id, escritorio_id, nome, categoria, conteudo_html,
                     campos_dinamicos, created_at)

-- Notas internas
notas               (id, escritorio_id, processo_id, cliente_id, usuario_id,
                     conteudo, created_at)

-- Notificações
notificacoes        (id, usuario_id, tipo, titulo, mensagem, lida,
                     link, created_at)

-- Audit log
audit_log           (id, usuario_id, escritorio_id, acao, tabela,
                     registro_id, dados_antes, dados_depois, ip, created_at)
```

---

## 🔗 Integrações a Implementar

| Integração | Finalidade | API |
|------------|-----------|-----|
| DataJud CNJ | Consulta processual | ✅ Implementado |
| ViaCEP | Busca por CEP | Gratuita |
| ReceitaWS | Consulta CNPJ | Gratuita |
| ANBIMA Feriados | Cálculo de prazos | Gratuita |
| OpenAI GPT-4o | IA jurídica | ✅ Implementado |
| Evolution API | WhatsApp | Pago |
| D4Sign | Assinatura digital | Pago |
| Mercado Pago | Cobranças | Pago |
| SendGrid | E-mail transacional | Freemium |
| Google Calendar | Agenda | OAuth |
| Sentry | Monitoramento de erros | Freemium |

---

*Última atualização: 2026-03-24*
*Versão: 1.0*
