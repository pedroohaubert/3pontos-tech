# Reddit Clone - 3Pontos Tech Challenge

## Visão Geral da Solução

Este projeto implementa um clone simplificado do Reddit seguindo a arquitetura proposta, com foco em funcionalidades essenciais de comunidades (subreddits), postagens em Markdown, comentários aninhados e sistema de votos (upvote/downvote).

### Stack Tecnológica

- **PHP 8.4** com **Laravel 12**
- **FilamentPHP 4** exclusivamente para painel administrativo
- **SQLite** para desenvolvimento (configurado para fácil migração para PostgreSQL/MySQL)
- **Blade Templates** + **TailwindCSS v4** para interface
- **Laravel Breeze** para autenticação

### Arquitetura

- **Backend-first approach** com foco em robustez
- **Thin controllers** delegando lógica para services
- **DTOs** para transferência de dados
- **Form Requests** para validação
- **Policies** para controle de acesso
- **Clean Architecture** com separação clara de responsabilidades

## Decisões Técnicas

### 1. Sistema de Autenticação

**Decisão:** Laravel Breeze (oficial)
**Justificativa:**

- Cumpre restrição de plugins externos (Breeze é pacote oficial Laravel)
- Proporciona sistema completo de auth (login/register/reset password)
- Views responsivas com TailwindCSS
- Integração nativa com Laravel Sanctum se necessário futura
- Trade-off: Menos customização vs. Rapidez de implementação

### 2. Sistema de Roles

**Decisão:** Campo `role` enum na tabela users
**Alternativas consideradas:**

- Spatie Laravel Permission (rejeitado por ser plugin externo)
- Gates/Policies customizados (mais complexo de manter)
- Tabela separada roles (overkill para apenas admin/user)
  **Justificativa:**
- Simples e suficiente para necessidades (admin vs user comum)
- Performance melhor que joins desnecessários
- Fácil de estender se necessário
- Trade-off: Menos flexível vs. Simplicidade

### 3. Controle de Acesso ao Filament

**Decisão:** Apenas usuários com role 'admin'
**Justificativa:**

- Segregação clara entre usuários comuns e administradores
- Previne acesso não autorizado ao painel de gerenciamento
- Alinha com requisitos de moderação de conteúdo

### 4. Estrutura de Diretórios

**Decisão:** Seguir convenções Laravel com organização clara

```
app/
├── Http/Controllers/     # Controllers web (thin controllers)
├── Models/              # Eloquent Models
├── Services/            # Business logic (a implementar)
├── DTOs/               # Data Transfer Objects (a implementar)
├── Policies/            # Authorization policies (a implementar)
└── Filament/           # Admin resources
```

### 5. Banco de Dados

**Decisão:** SQLite para desenvolvimento
**Justificativa:**

- Agilidade no setup conforme especificado no README
- Fácil migração para PostgreSQL/MySQL em produção
- Ambiente Docker preparado para diferentes SGBDs

### 6. Front-end

**Decisão:** Blade + TailwindCSS v4 (exclusivamente)
**Justificativa:**

- Segue especificação obrigatória do projeto
- Dois ambientes distintos: admin (Filament) e front-end (Blade)
- Sem JavaScript frameworks (React/Vue) conforme restrições
- Tailwind v4 para utilitários modernos de CSS
- FilamentPHP usado APENAS para painel administrativo

### 7. Separação de Ambientes

**Decisão:** Admin (FilamentPHP) vs Front-end (Blade + Tailwind)
**Justificativa:**

- Conforme especificado no README: "Serão dois ambientes: admin (Filament) e front-end (Blade + Tailwind)"
- Filament exclusivo para criação/gerenciamento de subreddits e moderação de posts
- Interface pública (home, subreddits, posts) desenvolvida com Blade + Tailwind
- Separação clara de responsabilidades e tecnologias

## Anotações do Processo

### Fase 1: Setup e Autenticação ✅ COMPLETO

- Laravel Breeze instalado e configurado
- Sistema de roles implementado
- User model atualizado com métodos helpers
- Migration criada e executada
- Views de auth criadas automaticamente

### Próximas Etapas

1. **Modelagem de Dados Core**: Criar migrations para Subreddits, Posts, Comments, Votes
2. **Business Logic**: Implementar Services e DTOs
3. **Thin Controllers**: Controllers delegando para services
4. **Authorization**: Policies para controle de acesso
5. **Admin Panel**: Filament resources para gestão
6. **Frontend**: Páginas públicas com Blade + Tailwind
7. **Features**: Sistema de votos, comentários aninhados
8. **Testing**: Cobertura completa com testes

## Trade-offs Conscientes

### 1. Simplicidade vs. Escalabilidade

- **Escolha:** Priorizei simplicidade para entrega no prazo
- **Risco:** Pode precisar refatorar para alta escala
- **Mitigação:** Arquitetura preparada para evolução

### 2. Breeze vs. Auth Customizado

- **Escolha:** Breeze para agilidade
- **Risco:** Menos controle sobre UX de auth
- **Mitigação:** Views customizáveis com Tailwind

### 3. Campo role vs. Package de Permission

- **Escolha:** Campo simples para atender requisitos
- **Risco:** Menos flexibilidade para permissões granulares
- **Mitigação:** Suficiente para admin/user, extensível se necessário

## Métricas de Qualidade

- PSR-12 compliance (via Pint)
- Type safety com PHP 8.4
- Conventional commits
- Documentação incremental
- Testes automatizados (planejados)

## Lições Aprendidas

1. **Setup inicial:** Breeze facilitou muito o início do projeto
2. **Roles:** Implementação simples mas efetiva
3. **Conventional commits:** Disciplina importante para rastreabilidade
4. **Backend-first:** Permite foco na lógica antes da apresentação
