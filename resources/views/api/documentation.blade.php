<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            TeamSphere API Dokumentacija
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Introduction -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-8 border border-gray-700/50 shadow-2xl mb-8">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-4">
                        TeamSphere REST API
                    </h1>
                    <p class="text-gray-300 text-lg max-w-3xl mx-auto">
                        Kompletan REST API za upravljanje sportskim ligama, mečevima, igračima i organizacijama.
                        Svi endpoint-i vraćaju konzistentan JSON format sa success, data i message poljima.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-700/30 rounded-xl p-6 text-center">
                        <div class="text-3xl mb-2">🚀</div>
                        <h3 class="text-white font-semibold mb-2">Versioniranje</h3>
                        <p class="text-gray-400 text-sm">API verzija v1 sa backward kompatibilnošću</p>
                    </div>
                    <div class="bg-gray-700/30 rounded-xl p-6 text-center">
                        <div class="text-3xl mb-2">🔐</div>
                        <h3 class="text-white font-semibold mb-2">Autentifikacija</h3>
                        <p class="text-gray-400 text-sm">Laravel Sanctum Bearer tokeni</p>
                    </div>
                    <div class="bg-gray-700/30 rounded-xl p-6 text-center">
                        <div class="text-3xl mb-2">📊</div>
                        <h3 class="text-white font-semibold mb-2">JSON Response</h3>
                        <p class="text-gray-400 text-sm">Konzistentan format za sve endpoint-e</p>
                    </div>
                </div>
            </div>

            <!-- Public Endpoints -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-8 border border-gray-700/50 shadow-2xl mb-8">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                    <span class="bg-green-500 text-white text-sm px-3 py-1 rounded-full mr-3">PUBLIC</span>
                    Javni Endpoint-i
                </h2>
                <p class="text-gray-400 mb-6">Ovi endpoint-i ne zahtijevaju autentifikaciju i dostupni su svima.</p>

                <div class="space-y-4">
                    @foreach($endpoints['public'] as $endpoint)
                    <div class="bg-gray-700/30 rounded-xl p-6 border border-gray-600/30">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded">{{ $endpoint['method'] }}</span>
                                <code class="text-gray-300 font-mono text-sm">{{ $endpoint['endpoint'] }}</code>
                            </div>
                            <button onclick="testEndpoint('{{ $endpoint['method'] }}', '{{ $endpoint['endpoint'] }}', [], '')"
                                    class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-3 py-1 rounded-lg transition-colors">
                                Testiraj
                            </button>
                        </div>
                        <p class="text-gray-300 mb-4">{{ $endpoint['description'] }}</p>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-white font-semibold mb-2">Primjer zahtjeva:</h4>
                                <pre class="bg-gray-900 rounded-lg p-3 text-green-400 text-sm overflow-x-auto"><code>{{ $endpoint['example_request'] }}</code></pre>
                            </div>
                            <div>
                                <h4 class="text-white font-semibold mb-2">Primjer responsa:</h4>
                                <pre class="bg-gray-900 rounded-lg p-3 text-blue-400 text-sm overflow-x-auto"><code>{{ $endpoint['example_response'] }}</code></pre>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Authenticated Endpoints -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-8 border border-gray-700/50 shadow-2xl mb-8">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                    <span class="bg-red-500 text-white text-sm px-3 py-1 rounded-full mr-3">AUTH</span>
                    Zaštićeni Endpoint-i
                </h2>
                <p class="text-gray-400 mb-6">Ovi endpoint-i zahtijevaju Bearer token u Authorization header-u.</p>

                <div class="bg-yellow-900/20 border border-yellow-600/30 rounded-xl p-4 mb-6">
                    <div class="flex items-center space-x-2">
                        <span class="text-yellow-400">⚠️</span>
                        <span class="text-yellow-300 text-sm">Za testiranje ovih endpoint-a potreban je validan Bearer token.</span>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($endpoints['authenticated'] as $endpoint)
                    <div class="bg-gray-700/30 rounded-xl p-6 border border-gray-600/30">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <span class="bg-{{ $endpoint['method'] == 'GET' ? 'green' : ($endpoint['method'] == 'POST' ? 'blue' : ($endpoint['method'] == 'PUT' ? 'yellow' : 'red')) }}-500 text-white text-xs font-bold px-2 py-1 rounded">{{ $endpoint['method'] }}</span>
                                <code class="text-gray-300 font-mono text-sm">{{ $endpoint['endpoint'] }}</code>
                            </div>
                            <button onclick="testAuthenticatedEndpoint('{{ $endpoint['method'] }}', '{{ $endpoint['endpoint'] }}', {{ isset($endpoint['headers']) ? json_encode($endpoint['headers']) : '[]' }}, '{{ isset($endpoint['example_request']) ? str_replace("'", "\\'", $endpoint['example_request']) : '' }}')"
                                    class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-3 py-1 rounded-lg transition-colors">
                                Testiraj
                            </button>
                        </div>
                        <p class="text-gray-300 mb-4">{{ $endpoint['description'] }}</p>

                        @if(isset($endpoint['headers']) && count($endpoint['headers']) > 0)
                        <div class="mb-4">
                            <h4 class="text-white font-semibold mb-2">Headers:</h4>
                            <div class="bg-gray-900 rounded-lg p-3">
                                @foreach($endpoint['headers'] as $header)
                                <code class="text-orange-400 text-sm block">{{ $header }}</code>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-white font-semibold mb-2">Primjer zahtjeva:</h4>
                                <pre class="bg-gray-900 rounded-lg p-3 text-green-400 text-sm overflow-x-auto"><code>{{ $endpoint['example_request'] }}</code></pre>
                            </div>
                            <div>
                                <h4 class="text-white font-semibold mb-2">Primjer responsa:</h4>
                                <pre class="bg-gray-900 rounded-lg p-3 text-blue-400 text-sm overflow-x-auto"><code>{{ $endpoint['example_response'] }}</code></pre>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- API Tester Modal -->
            <div id="apiTesterModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="bg-gray-800 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-white">API Tester</h3>
                                <button onclick="closeApiTester()" class="text-gray-400 hover:text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-white font-semibold mb-3">Zahtjev</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Method</label>
                                            <select id="testMethod" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white">
                                                <option value="GET">GET</option>
                                                <option value="POST">POST</option>
                                                <option value="PUT">PUT</option>
                                                <option value="DELETE">DELETE</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Endpoint</label>
                                            <input id="testEndpoint" type="text" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white" placeholder="/api/v1/sports">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Headers</label>
                                            <textarea id="testHeaders" rows="3" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white font-mono text-sm" placeholder='{"Authorization": "Bearer {token}", "Content-Type": "application/json"}'></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Body (JSON)</label>
                                            <textarea id="testBody" rows="5" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white font-mono text-sm" placeholder='{"name": "Test"}'></textarea>
                                        </div>
                                        <button onclick="sendApiRequest()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                                            Pošalji zahtjev
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-white font-semibold mb-3">Response</h4>
                                    <div class="bg-gray-900 rounded-lg p-4 h-96 overflow-y-auto">
                                        <pre id="apiResponse" class="text-green-400 text-sm font-mono whitespace-pre-wrap">Kliknite "Pošalji zahtjev" da testirate API endpoint.</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentTestData = {};

        function testEndpoint(method, endpoint, headers, body) {
            currentTestData = { method, endpoint, headers, body };
            document.getElementById('testMethod').value = method;
            document.getElementById('testEndpoint').value = endpoint;
            document.getElementById('testHeaders').value = JSON.stringify(headers, null, 2);
            document.getElementById('testBody').value = body.replace(/^[A-Z]+ .*\n/, '').trim();
            document.getElementById('apiTesterModal').classList.remove('hidden');
        }

        function testAuthenticatedEndpoint(method, endpoint, headers, body) {
            testEndpoint(method, endpoint, headers, body);
        }

        function closeApiTester() {
            document.getElementById('apiTesterModal').classList.add('hidden');
        }

        async function sendApiRequest() {
            const method = document.getElementById('testMethod').value;
            const endpoint = document.getElementById('testEndpoint').value;
            const headersText = document.getElementById('testHeaders').value;
            const bodyText = document.getElementById('testBody').value;

            const responseElement = document.getElementById('apiResponse');
            responseElement.textContent = 'Šaljem zahtjev...';

            try {
                let headers = {};
                if (headersText.trim()) {
                    headers = JSON.parse(headersText);
                }

                const config = {
                    method: method,
                    headers: {
                        'Accept': 'application/json',
                        ...headers
                    }
                };

                if (['POST', 'PUT', 'PATCH'].includes(method) && bodyText.trim()) {
                    config.headers['Content-Type'] = 'application/json';
                    config.body = bodyText;
                }

                const response = await fetch(endpoint, config);
                const data = await response.text();

                let formattedData;
                try {
                    const jsonData = JSON.parse(data);
                    formattedData = JSON.stringify(jsonData, null, 2);
                } catch {
                    formattedData = data;
                }

                responseElement.innerHTML = `<div class="mb-2 text-yellow-400">Status: ${response.status} ${response.statusText}</div><div class="text-green-400">${formattedData}</div>`;

            } catch (error) {
                responseElement.innerHTML = `<div class="text-red-400">Greška: ${error.message}</div>`;
            }
        }

        // Close modal when clicking outside
        document.getElementById('apiTesterModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApiTester();
            }
        });
    </script>
</x-app-layout>