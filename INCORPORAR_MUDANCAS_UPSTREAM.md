# 🔄 Guia: Incorporar Mudanças do Projeto Original (Upstream)

Este repositório é um **FORK** de outro projeto. Este guia explica como incorporar as mudanças do projeto original (upstream) no seu fork.

## 📋 Pré-requisitos

1. Repositório Git inicializado
2. URL do repositório original (upstream)
3. Branch principal configurada (geralmente `main` ou `master`)

## 🚀 Passo a Passo

### 1. Verificar se o Git está inicializado

```bash
git status
```

Se retornar erro "not a git repository", inicialize o repositório:

```bash
git init
git add .
git commit -m "Commit inicial"
```

### 2. Configurar o Remote Upstream

Adicione o repositório original como upstream:

```bash
git remote add upstream URL_DO_REPOSITORIO_ORIGINAL
```

Exemplo:
```bash
git remote add upstream https://github.com/usuario-original/projeto-original.git
```

### 3. Verificar os Remotes Configurados

```bash
git remote -v
```

Você deve ver:
- `origin`: Seu fork (seu repositório)
- `upstream`: Repositório original

### 4. Buscar Mudanças do Upstream

```bash
git fetch upstream
```

### 5. Verificar a Branch Atual

```bash
git branch
```

Certifique-se de estar na branch principal (geralmente `main` ou `master`).

### 6. Incorporar as Mudanças

Você tem duas opções:

#### Opção A: Merge (Recomendado para iniciantes)

```bash
git checkout main
git merge upstream/main
```

#### Opção B: Rebase (Mantém histórico limpo)

```bash
git checkout main
git rebase upstream/main
```

**⚠️ Atenção**: Se você já fez push das suas mudanças, usar rebase pode causar problemas. Prefira merge nesses casos.

### 7. Resolver Conflitos (se houver)

Se houver conflitos, o Git mostrará os arquivos com conflito:

```bash
git status
```

Para cada arquivo com conflito:
1. Abra o arquivo no editor
2. Procure por marcadores de conflito (`<<<<<<<`, `=======`, `>>>>>>>`)
3. Resolva os conflitos manualmente
4. Após resolver, adicione o arquivo:

```bash
git add arquivo-resolvido.php
```

5. Continue o merge/rebase:

```bash
# Se estava fazendo merge:
git commit

# Se estava fazendo rebase:
git rebase --continue
```

### 8. Enviar as Mudanças para Seu Fork

Após incorporar as mudanças com sucesso:

```bash
git push origin main
```

Se usou rebase e já havia commits enviados:

```bash
git push origin main --force-with-lease
```

**⚠️ Cuidado**: Use `--force-with-lease` apenas se tiver certeza!

## 🔧 Script Automatizado

Para facilitar, criamos um script `sync-upstream.sh` que automatiza esse processo:

```bash
./scripts/sync-upstream.sh
```

## 📝 Checklist Rápido

- [ ] Repositório Git inicializado
- [ ] Remote upstream configurado
- [ ] Branch principal verificada
- [ ] Mudanças do upstream buscadas (`git fetch upstream`)
- [ ] Mudanças incorporadas (merge ou rebase)
- [ ] Conflitos resolvidos (se houver)
- [ ] Mudanças enviadas para seu fork (`git push`)

## ❓ Problemas Comuns

### Erro: "fatal: refusing to merge unrelated histories"

Se os repositórios tiverem históricos completamente diferentes:

```bash
git merge upstream/main --allow-unrelated-histories
```

### Erro: "remote upstream already exists"

O upstream já está configurado. Para atualizar a URL:

```bash
git remote set-url upstream NOVA_URL
```

### Ver mudanças antes de incorporar

```bash
git fetch upstream
git log HEAD..upstream/main --oneline
```

### Desfazer um merge/rebase que deu errado

```bash
git merge --abort  # Para merge
# ou
git rebase --abort  # Para rebase
```

## 📚 Recursos Adicionais

- [Git Documentation - Working with Remotes](https://git-scm.com/book/en/v2/Git-Basics-Working-with-Remotes)
- [GitHub Docs - Syncing a Fork](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/working-with-forks/syncing-a-fork)

