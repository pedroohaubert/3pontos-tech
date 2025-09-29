# Reddit Clone Development Plan

## Visão Geral

Desenvolvimento de um clone do Reddit seguindo stack obrigatória (Laravel 12 + FilamentPHP 4 + TailwindCSS v4), com ênfase em arquitetura clean, backend-first e estrutura organizada com thin controllers.

## Análise das Restrições do Projeto

### ✅ Laravel Breeze - PERMITIDO

- **Justificativa**: O README.md proíbe apenas "Plugins externos fora o MediaLibrary". Laravel Breeze é um pacote oficial do Laravel, não um plugin externo.
- **Estado atual**: Nenhum sistema de auth implementado ainda.
- **Decisão**: Usar Laravel Breeze para autenticação básica (login/register/logout).

## Fase 1: Setup e Autenticação (Backend Foundation)

### 1.1 Instalar Laravel Breeze ✅ COMPLETO

- Instalar Laravel Breeze para sistema de autenticação
- Configurar rotas de auth
- Customizar views do Breeze com TailwindCSS
- Testar fluxo básico de login/registro
- **Justificativa**: Necessário para usuários criarem posts/comentários/votos

```bash
composer require laravel/breeze --dev ✅
php artisan breeze:install blade ✅
php artisan migrate ✅ (tabelas password_reset_tokens criadas)
npm install && npm run dev ✅
```

### 1.2 Modificar User Model para Roles ✅ COMPLETO

- Adicionar campo `role` (enum: admin, user)
- Criar migration para adicionar coluna role
- Implementar métodos helpers (isAdmin(), isUser())
- Configurar FilamentUser para admins

```bash
php artisan make:migration add_role_to_users_table --table=users ✅
php artisan migrate ✅ (campo role adicionado com default 'user')
# UserFactory admin() method atualizado para definir role='admin'
# Migration criada para atualizar usuário admin existente para role='admin'
php artisan make:migration update_admin_user_role --table=users ✅
php artisan migrate ✅
```

### 1.3 Modelagem de Dados Core

- Criar migrations para: Subreddits, Posts, Comments, Votes
- Definir relacionamentos Eloquent
- Implementar soft deletes onde apropriado
- Criar factories para seeding

### 1.4 Models e Relacionamentos

- User model (já existe, ajustar relacionamentos)
- Subreddit model
- Post model (com suporte a Markdown)
- Comment model (aninhado)
- Vote model (upvote/downvote)

## Fase 2: Business Logic (Services & DTOs)

### 2.1 Criar DTOs

- UserDTO, SubredditDTO, PostDTO, CommentDTO, VoteDTO
- Form request DTOs para validação

### 2.2 Implementar Services

- SubredditService (CRUD operations)
- PostService (create, update, delete, markdown parsing)
- CommentService (nested comments, threading)
- VoteService (upvote/downvote logic)
- UserService (profile management)

### 2.3 Form Requests & Validation

- StorePostRequest, UpdatePostRequest
- StoreCommentRequest, UpdateCommentRequest
- CreateSubredditRequest
- Custom validation rules para Markdown, unique votes, etc.

## Fase 3: Controllers & Policies (API Layer)

### 3.1 Thin Controllers

- SubredditController (index, show, store, update, destroy)
- PostController (index, show, store, update, destroy)
- CommentController (store, update, destroy, nested operations)
- VoteController (upvote, downvote, remove vote)

### 3.2 Authorization Policies

- SubredditPolicy (create, update, delete)
- PostPolicy (create, update, delete, view)
- CommentPolicy (create, update, delete)
- VotePolicy (vote, unvote)

### 3.3 API Routes

- Definir rotas RESTful
- Agrupar por prefixos (api/v1)
- Middleware de autenticação

## Fase 4: Admin Panel (FilamentPHP - EXCLUSIVO)

### 4.1 Filament Resources

- SubredditResource (CRUD completo para criação dinâmica de comunidades)
- PostResource (CRUD, moderação de conteúdo)
- UserResource (admin management e role assignment)
- CommentResource (moderação de comentários)

### 4.2 Filament Widgets & Actions

- Dashboard widgets (stats, recent posts, user activity)
- Bulk actions para moderação em lote
- Custom actions para featured posts e community management

## Fase 5: Frontend (Blade + TailwindCSS - EXCLUSIVO)

### 5.1 Layouts Base

- Master layout com Tailwind
- Navigation components
- Responsive design structure

### 5.2 Páginas Públicas

- Home/Dashboard (lista posts, trending subreddits)
- Subreddit page (posts da comunidade)
- Post detail page (post + comentários aninhados)
- User profile page

### 5.3 Componentes Reutilizáveis

- PostCard component
- CommentThread component
- VoteButtons component
- Markdown renderer
- Pagination components

### 5.4 Formulários

- Create post form (com Markdown editor)
- Create subreddit form
- Comment form
- Search functionality

## Fase 6: Features Avançadas

### 6.1 Sistema de Votos

- Upvote/downvote com AJAX
- Vote counting e ordering
- Karma system (opcional)

### 6.2 Moderação

- Report system
- Content moderation queue
- User banning capabilities

### 6.3 Performance & UX

- Pagination infinita
- Real-time updates (WebSockets opcional)
- Caching estratégico
- SEO optimization

## Fase 7: Testing & Quality Assurance

### 7.1 Testes Unitários

- Services tests
- Models tests
- Policies tests

### 7.2 Testes de Feature

- CRUD operations
- Authentication flows
- Vote system

### 7.3 Testes E2E (Browser)

- User journeys
- Admin workflows

## Fase 8: Deployment & Documentation

### 8.1 Documentação

- Atualizar DEVELOPMENT.md com decisões técnicas
- API documentation
- Deployment guide

### 8.2 Finalização

- Performance optimization
- Security audit
- Production deployment setup
