# 🎨 Como Adicionar Logomarca e Favicon

## 📁 Onde colocar as imagens:

### **Logomarca:**
- **Pasta:** `public/images/logo/`
- **Formatos recomendados:** PNG, SVG, JPG
- **Tamanhos sugeridos:** 
  - Desktop: 200x60px ou 300x90px
  - Mobile: 150x45px
- **Nome do arquivo:** `logo.png` ou `logo.svg`

### **Favicon:**
- **Pasta:** `public/images/icons/`
- **Formatos:** ICO, PNG, SVG
- **Tamanhos necessários:**
  - 16x16px (favicon.ico)
  - 32x32px (favicon-32x32.png)
  - 192x192px (android-chrome-192x192.png)
  - 512x512px (android-chrome-512x512.png)
- **Nome do arquivo:** `favicon.ico`

## 🚀 Passos para adicionar:

1. **Copie sua logomarca** para `public/images/logo/logo.png`
2. **Copie seu ícone** para `public/images/icons/favicon.ico`
3. **Execute o comando** para atualizar as referências no código

## 📝 Após adicionar as imagens, execute:

```bash
php artisan make:command UpdateLogoAndFavicon
```

Isso criará um comando que irá:
- Atualizar o layout principal
- Adicionar a logomarca na navegação
- Configurar o favicon
- Otimizar as imagens se necessário

## 🎯 Resultado esperado:

- **Logomarca** aparecerá no topo esquerdo da navegação
- **Favicon** aparecerá na aba do navegador
- **Responsivo** para desktop e mobile
- **Otimizado** para performance
