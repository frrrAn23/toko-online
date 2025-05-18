<!DOCTYPE html>
<html>

<head>
    <title>Cek Ongkir</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
</head>

<body>
    <form id="ongkirForm">
   
        <label>Lokasi Tujuan:</label><br>
        <select id="destination" placeholder="Ketik kota atau provinsi..."></select>
        <br><br>

        <label>Berat (gram):</label><br>
        <input type="number" name="weight" id="weight" placeholder="Berat (gram)" required><br><br>

        <label>Kurir:</label><br>
        <select name="courier" id="courier" required>
            <option value="">Pilih Kurir</option>
            <option value="jne">JNE</option>
            <option value="tiki">TIKI</option>
            <option value="pos">POS Indonesia</option>
        </select><br><br>

        <button type="submit">Cek Ongkir</button>
    </form>

    <div id="result" style="margin-top: 20px;"></div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Tom Select for destination search
        const destinationSelect = document.getElementById('destination');
        const resultDiv = document.getElementById('result');
        
        if (destinationSelect) {
            const tom = new TomSelect(destinationSelect, {
                valueField: 'id',
                labelField: 'label',
                searchField: ['label'],
                load: function (query, callback) {
                    if (query.length < 3) return callback();

                    fetch(`/location?keyword=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.meta && data.meta.code === 200 && Array.isArray(data.data)) {
                                callback(data.data);
                            } else {
                                callback();
                            }
                        })
                        .catch(() => callback());
                }
            });
        }

        // Load provinces dropdown
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        
        if (provinceSelect) {
            fetch('/provinces')
                .then(response => response.json())
                .then(data => {
                    console.log('Provinces data:', data);
                    if (data.rajaongkir.status.code === 200) {
                        let provinces = data.rajaongkir.results;
                        provinces.forEach(province => {
                            let option = document.createElement('option');
                            option.value = province.province_id;
                            option.textContent = province.province;
                            provinceSelect.appendChild(option);
                        });
                    } else {
                        console.error('Failed to fetch provinces', data.rajaongkir.status.description);
                    }
                })
                .catch(error => {
                    console.error('Error fetching provinces:', error);
                });
        }

        // Handle province change to load cities
        if (provinceSelect && citySelect) {
            provinceSelect.addEventListener('change', function() {
                let provinceId = this.value;
                fetch(`/cities?province_id=${provinceId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Cities data:', data);
                        if (data.rajaongkir.status.code === 200) {
                            let cities = data.rajaongkir.results;
                            citySelect.innerHTML = '';
                            cities.forEach(city => {
                                let option = document.createElement('option');
                                option.value = city.city_id;
                                option.textContent = city.city_name;
                                citySelect.appendChild(option);
                            });
                        } else {
                            console.error('Failed to fetch cities', data.rajaongkir.status.description);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching cities:', error);
                    });
            });
        }

        // Handle form submission
        document.getElementById('ongkirForm').addEventListener('submit', function (event) {
            event.preventDefault();

            const origin = 31555; // Default origin ID from first script
            // Try to get destination from both possible sources
            const destination = destinationSelect ? destinationSelect.value : document.getElementById('city').value;
            const weight = document.getElementById('weight').value;
            const courier = document.getElementById('courier').value;

            // Validation
            if (!destination || !weight || !courier) {
                alert('Pastikan semua kolom terisi dengan benar!');
                return;
            }

            fetch('/cost', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    origin,
                    destination,
                    weight,
                    courier
                })
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.innerHTML = '';
                console.log('Response dari server:', data);

                // Handle both possible response formats
                if (data.rajaongkir && data.rajaongkir.status.code === 200) {
                    // RajaOngkir format
                    let result = data.rajaongkir.results[0].costs;
                    result.forEach(cost => {
                        const div = document.createElement('div');
                        div.textContent = `${cost.service} : ${cost.cost[0].value} Rupiah (${cost.cost[0].etd} hari)`;
                        resultDiv.appendChild(div);
                    });
                } else if (data.meta?.code === 200 && Array.isArray(data.data)) {
                    // Alternative format
                    data.data.forEach(service => {
                        const value = service.cost;
                        const etd = service.etd;
                        const serviceName = service.service;

                        const div = document.createElement('div');
                        div.textContent = `${serviceName} : ${value} Rupiah (${etd} hari)`;
                        resultDiv.appendChild(div);
                    });
                } else {
                    resultDiv.textContent = 'Gagal mendapatkan biaya pengiriman.';
                    console.warn('Struktur data tidak sesuai:', data);
                }
            })
            .catch(error => {
                console.error('Error fetching cost:', error);
                resultDiv.textContent = 'Terjadi kesalahan saat memproses permintaan.';
            });
        });
    });
</script>
</body>

</html>