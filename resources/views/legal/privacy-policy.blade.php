@extends('layouts.profile')

@section('title', 'Política de Privacidade - Amigos Para Sempre')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                <i class="fas fa-shield-alt text-pink-500 mr-3"></i>Política de Privacidade
            </h1>
            <p class="text-lg text-gray-600">Última atualização: {{ date('d/m/Y') }}</p>
        </div>

        <!-- Introduction -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Introdução</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                A <strong>Amigos Para Sempre</strong> ("nós", "nosso" ou "aplicativo") valoriza sua privacidade e está comprometida em proteger seus dados pessoais. Esta Política de Privacidade explica como coletamos, usamos, armazenamos e protegemos suas informações quando você utiliza nosso aplicativo de relacionamentos.
            </p>
            <p class="text-gray-700 leading-relaxed">
                Ao usar nosso serviço, você concorda com a coleta e uso de informações de acordo com esta política. Se você não concordar com os termos desta política, por favor, não use nosso aplicativo.
            </p>
        </div>

        <!-- Data Collection -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Informações que Coletamos</h2>
            
            <h3 class="text-xl font-medium text-gray-800 mb-3">2.1 Informações Fornecidas por Você</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li><strong>Informações de Conta:</strong> Nome, sobrenome, data de nascimento, gênero, e-mail</li>
                <li><strong>Informações de Perfil:</strong> Biografia, objetivos de relacionamento, nível de educação, hábitos (fumar, beber)</li>
                <li><strong>Fotos e Mídia:</strong> Fotos de perfil e galeria de imagens</li>
                <li><strong>Localização:</strong> Cidade, estado, país e coordenadas geográficas (quando permitido)</li>
                <li><strong>Interesses e Preferências:</strong> Interesses pessoais, traços de personalidade, preferências de matching</li>
                <li><strong>Perfil Psicológico:</strong> Respostas a questionários de personalidade</li>
                <li><strong>Comunicações:</strong> Mensagens trocadas com outros usuários</li>
            </ul>

            <h3 class="text-xl font-medium text-gray-800 mb-3">2.2 Informações Coletadas Automaticamente</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li><strong>Dados de Uso:</strong> Páginas visitadas, tempo de permanência, cliques e interações</li>
                <li><strong>Informações do Dispositivo:</strong> Tipo de dispositivo, sistema operacional, navegador</li>
                <li><strong>Dados de Localização:</strong> Coordenadas GPS (quando permitido)</li>
                <li><strong>Logs de Sistema:</strong> Endereços IP, timestamps, erros e falhas</li>
                <li><strong>Cookies e Tecnologias Similares:</strong> Para melhorar sua experiência</li>
            </ul>
        </div>

        <!-- Data Usage -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Como Usamos Suas Informações</h2>
            
            <h3 class="text-xl font-medium text-gray-800 mb-3">3.1 Finalidades Principais</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li><strong>Fornecimento do Serviço:</strong> Criar e manter sua conta, facilitar conexões</li>
                <li><strong>Matching e Recomendações:</strong> Sugerir perfis compatíveis baseados em suas preferências</li>
                <li><strong>Comunicação:</strong> Permitir mensagens entre usuários e notificações do sistema</li>
                <li><strong>Segurança:</strong> Verificar identidades, prevenir fraudes e manter a segurança</li>
                <li><strong>Melhoria do Serviço:</strong> Analisar uso para melhorar funcionalidades</li>
            </ul>

            <h3 class="text-xl font-medium text-gray-800 mb-3">3.2 Finalidades Secundárias</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li><strong>Marketing:</strong> Enviar ofertas e novidades (com seu consentimento)</li>
                <li><strong>Pesquisas:</strong> Realizar estudos para melhorar o serviço</li>
                <li><strong>Suporte:</strong> Responder a suas dúvidas e solicitações</li>
                <li><strong>Conformidade Legal:</strong> Cumprir obrigações legais e regulamentares</li>
            </ul>
        </div>

        <!-- Data Sharing -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Compartilhamento de Informações</h2>
            
            <h3 class="text-xl font-medium text-gray-800 mb-3">4.1 Compartilhamento com Outros Usuários</h3>
            <p class="text-gray-700 leading-relaxed mb-4">
                Suas informações de perfil (nome, idade, fotos, biografia, interesses) são visíveis para outros usuários do aplicativo para facilitar conexões e matching.
            </p>

            <h3 class="text-xl font-medium text-gray-800 mb-3">4.2 Compartilhamento com Terceiros</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li><strong>Prestadores de Serviço:</strong> Empresas que nos ajudam a operar o aplicativo (hospedagem, análise, pagamentos)</li>
                <li><strong>Autoridades Legais:</strong> Quando exigido por lei ou para proteger direitos</li>
                <li><strong>Transações Empresariais:</strong> Em caso de fusão, aquisição ou venda de ativos</li>
                <li><strong>Com seu Consentimento:</strong> Em outras situações com sua autorização explícita</li>
            </ul>
        </div>

        <!-- Data Security -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Segurança dos Dados</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Implementamos medidas de segurança técnicas e organizacionais para proteger suas informações:
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li><strong>Criptografia:</strong> Dados sensíveis são criptografados em trânsito e em repouso</li>
                <li><strong>Controle de Acesso:</strong> Apenas pessoal autorizado tem acesso aos dados</li>
                <li><strong>Monitoramento:</strong> Sistemas de detecção de intrusão e monitoramento contínuo</li>
                <li><strong>Backup Seguro:</strong> Cópias de segurança regulares e seguras</li>
                <li><strong>Atualizações:</strong> Manutenção regular de sistemas e correção de vulnerabilidades</li>
            </ul>
        </div>

        <!-- User Rights -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Seus Direitos (LGPD/GDPR)</h2>
            
            <h3 class="text-xl font-medium text-gray-800 mb-3">6.1 Direitos Fundamentais</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li><strong>Acesso:</strong> Solicitar informações sobre seus dados pessoais</li>
                <li><strong>Correção:</strong> Retificar dados incorretos ou incompletos</li>
                <li><strong>Exclusão:</strong> Solicitar a remoção de seus dados ("direito ao esquecimento")</li>
                <li><strong>Portabilidade:</strong> Receber seus dados em formato estruturado</li>
                <li><strong>Oposição:</strong> Opor-se ao processamento de seus dados</li>
                <li><strong>Limitação:</strong> Restringir o processamento em certas circunstâncias</li>
            </ul>

            <h3 class="text-xl font-medium text-gray-800 mb-3">6.2 Como Exercer Seus Direitos</h3>
            <p class="text-gray-700 leading-relaxed mb-4">
                Para exercer qualquer um desses direitos, entre em contato conosco através do e-mail:
            </p>
            <div class="bg-pink-50 border border-pink-200 rounded-lg p-4 mb-6">
                <p class="text-pink-800 font-medium">
                    <i class="fas fa-envelope mr-2"></i>suporte@amigosparasempre.com
                </p>
                <p class="text-pink-700 text-sm mt-1">
                    Responderemos em até 15 dias úteis conforme a LGPD
                </p>
            </div>
        </div>

        <!-- Data Retention -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Retenção de Dados</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Mantemos suas informações pelo tempo necessário para cumprir as finalidades descritas nesta política:
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li><strong>Conta Ativa:</strong> Enquanto sua conta estiver ativa e você usar o serviço</li>
                <li><strong>Conta Inativa:</strong> Até 2 anos após inatividade, com notificação prévia</li>
                <li><strong>Dados Legais:</strong> Conforme exigido por lei (até 5 anos para fins fiscais)</li>
                <li><strong>Dados de Comunicação:</strong> Até 1 ano após a última mensagem</li>
            </ul>
        </div>

        <!-- Cookies -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Cookies e Tecnologias Similares</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Utilizamos cookies e tecnologias similares para melhorar sua experiência:
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li><strong>Cookies Essenciais:</strong> Necessários para o funcionamento básico do aplicativo</li>
                <li><strong>Cookies de Performance:</strong> Para analisar como você usa o aplicativo</li>
                <li><strong>Cookies de Funcionalidade:</strong> Para lembrar suas preferências</li>
                <li><strong>Cookies de Marketing:</strong> Para personalizar anúncios (com seu consentimento)</li>
            </ul>
            <p class="text-gray-700 leading-relaxed">
                Você pode gerenciar suas preferências de cookies através das configurações do seu navegador.
            </p>
        </div>

        <!-- International Transfers -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Transferências Internacionais</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Seus dados podem ser transferidos e processados em países fora do Brasil. Quando isso ocorrer, garantimos que:
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li>O país de destino ofereça nível adequado de proteção de dados</li>
                <li>Existam salvaguardas apropriadas (cláusulas contratuais padrão)</li>
                <li>Você tenha dado consentimento explícito para a transferência</li>
            </ul>
        </div>

        <!-- Children's Privacy -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">10. Privacidade de Menores</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Nosso aplicativo é destinado a pessoas com 18 anos ou mais. Não coletamos intencionalmente informações de menores de idade. Se descobrirmos que coletamos dados de um menor, tomaremos medidas para excluir essas informações imediatamente.
            </p>
        </div>

        <!-- Policy Changes -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Alterações nesta Política</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Podemos atualizar esta Política de Privacidade periodicamente. Quando fizermos alterações significativas, notificaremos você através de:
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li>Notificação no aplicativo</li>
                <li>E-mail para o endereço cadastrado</li>
                <li>Atualização da data "Última atualização" no topo desta página</li>
            </ul>
            <p class="text-gray-700 leading-relaxed">
                Recomendamos que você revise esta política regularmente para se manter informado sobre como protegemos suas informações.
            </p>
        </div>

        <!-- Contact Information -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">12. Contato</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Se você tiver dúvidas sobre esta Política de Privacidade ou sobre como tratamos seus dados pessoais, entre em contato conosco:
            </p>
            
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informações de Contato</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-pink-500 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-800">E-mail de Privacidade</p>
                            <p class="text-gray-600">suporte@amigosparasempre.com</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock text-pink-500 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-800">Tempo de Resposta</p>
                            <p class="text-gray-600">Até 15 dias úteis (conforme LGPD)</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-pink-500 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-800">Encarregado de Dados (DPO)</p>
                            <p class="text-gray-600">Disponível através do e-mail acima</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 pt-6 text-center">
            <p class="text-gray-600 text-sm">
                Esta Política de Privacidade é efetiva a partir de {{ date('d/m/Y') }} e foi elaborada em conformidade com a Lei Geral de Proteção de Dados (LGPD - Lei 13.709/2018) e o Regulamento Geral sobre a Proteção de Dados (GDPR) da União Europeia.
            </p>
        </div>
    </div>
</div>
@endsection
