# Reddit Clone Development Plan

## Progresso Atual

- ✅ **Fase 1: Setup e Autenticação** - COMPLETO
- ✅ **Fase 2: Business Logic (Services & DTOs)** - COMPLETO
- 🔄 **Próximo: Fase 3 - Controllers & Policies (API Layer)**

## Conquistas Implementadas

### 📊 Estatísticas do Projeto

- **21 arquivos PHP** criados/modificados
- **465 linhas** de comentários redundantes removidas
- **5 Models** com relacionamentos Eloquent completos
- **5 DTOs** para transferência de dados
- **5 Services** com lógica de negócio encapsulada
- **9 Form Requests** com validação avançada
- **11 Migrations** para estrutura de dados
- **5 Factories** para seeding de dados

### 🏗️ Arquitetura Implementada

- **Clean Architecture** com thin controllers
- **Self-documenting code** (métodos sem comentários óbvios)
- **Laravel 12 + FilamentPHP 4** stack obrigatória
- **Markdown parsing** para conteúdo de posts
- **Sistema de comentários aninhados** com limite de profundidade
- **Sistema de votos** (upvote/downvote) com prevenção de auto-voto
- **Authorization policies** integradas em Form Requests

## Decisões Técnicas Importantes

### 🎯 Clean Code & Self-Documenting Code

- **Princípio adotado**: "Código que não precisa de comentários é código bem escrito"
- **Implementação**: Métodos como `createPost()`, `updateUser()`, `isReply()` são auto-explicativos
- **Resultado**: 465 linhas de comentários redundantes removidas
- **Benefício**: Manutenibilidade e legibilidade aumentadas

### 🏛️ Arquitetura Thin Controllers

- **Controllers**: Apenas coordenam requests (thin controllers)
- **Services**: Contém toda lógica de negócio
- **DTOs**: Transferência de dados entre camadas
- **Form Requests**: Validação e conversão para DTOs

### 🔐 Sistema de Autorização

- **Implementação**: Authorization diretamente nos Form Requests
- **Benefício**: Validação precoce e consistente
- **Padrão**: `auth()->check() && auth()->id() === $resource->user_id || auth()->user()->isAdmin()`

### 🗃️ Modelagem de Dados

- **Soft Deletes**: Implementado em Posts e Comments para moderação
- **Polymorphic Relations**: Sistema de votos funciona com Posts e Comments
- **Indexes Estratégicos**: Performance otimizada para queries comuns

## Visão Geral

Desenvolvimento de um clone do Reddit seguindo stack obrigatória (Laravel 12 + FilamentPHP 4 + TailwindCSS v4), com ênfase em arquitetura clean, backend-first e estrutura organizada com thin controllers.

## Análise das Restrições do Projeto

### ✅ Laravel Breeze - IMPLEMENTADO

- **Justificativa**: O README.md proíbe apenas "Plugins externos fora o MediaLibrary". Laravel Breeze é um pacote oficial do Laravel, não um plugin externo.
- **Estado atual**: Sistema de autenticação completo com Breeze implementado.
- **Implementação**: Login/register/logout, views customizadas com TailwindCSS, rotas de auth configuradas.

## Fase 1: Setup e Autenticação (Backend Foundation) ✅ COMPLETO

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

### 1.3 Modelagem de Dados Core ✅ COMPLETO

- ✅ Criar migrations para: Subreddits, Posts, Comments, Votes
- ✅ Definir relacionamentos Eloquent
- ✅ Implementar soft deletes onde apropriado
- ✅ Criar factories para seeding

### 1.4 Models e Relacionamentos ✅ COMPLETO

- ✅ User model (já existe, ajustar relacionamentos)
- ✅ Subreddit model
- ✅ Post model (com suporte a Markdown)
- ✅ Comment model (aninhado)
- ✅ Vote model (upvote/downvote)

## Fase 2: Business Logic (Services & DTOs) ✅ COMPLETO

### 2.1 Criar DTOs ✅ COMPLETO

- ✅ UserDTO, SubredditDTO, PostDTO, CommentDTO, VoteDTO
- ✅ Form request DTOs para validação
- ✅ Self-documenting code (métodos sem comentários redundantes)

### 2.2 Implementar Services ✅ COMPLETO

- ✅ SubredditService (CRUD operations)
- ✅ PostService (create, update, delete, markdown parsing)
- ✅ CommentService (nested comments, threading)
- ✅ VoteService (upvote/downvote logic)
- ✅ UserService (profile management)
- ✅ Clean architecture com thin controllers

### 2.3 Form Requests & Validation ✅ COMPLETO

- ✅ StorePostRequest, UpdatePostRequest
- ✅ StoreCommentRequest, UpdateCommentRequest
- ✅ CreateSubredditRequest, VoteRequest
- ✅ Custom validation rules para Markdown, unique votes, depth limits
- ✅ Authorization checks integrados

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
