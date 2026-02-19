
/* =========================================================
   PETBIO – BÚSQUEDA PÚBLICA DE MASCOTA
   Fecha: 12-01-2026
   ========================================================= */

console.log("🔥 busqueda_publica.js cargado", new Date().toISOString());

document.addEventListener('DOMContentLoaded', () => {

  /* ===============================
     REFERENCIAS DOM
  =============================== */
  const btnEncontre = document.getElementById('btn-encontre');
  const contenedor  = document.getElementById('busqueda-mascota');
  const btnBuscar   = document.getElementById('btn-buscar');
  const input       = document.getElementById('codigo-mascota');                     const resultado   = document.getElementById('resultado-busqueda');

  if (!btnEncontre || !contenedor || !btnBuscar || !input || !resultado) {
    console.error("❌ DOM incompleto para búsqueda pública");
    return;
  }

  console.log("✅ Búsqueda pública inicializada");                                                                                                                      /* ===============================
     MOSTRAR / OCULTAR BUSCADOR                                                      =============================== */
  btnEncontre.addEventListener('click', () => {
    contenedor.classList.toggle('hidden');
    input.focus();
  });                                                                              
  /* ===============================                                                    BUSCAR MASCOTA                                                                  =============================== */                                                 btnBuscar.addEventListener('click', async (e) => {                                   e.preventDefault();                                                            
    const codigo = input.value.replace(/\D/g, '').trim();

    if (!/^\d{6}$/.test(codigo)) {                                                       resultado.innerHTML = `                                                              <p class="text-red-600 font-medium">                                                 ⚠️ Ingresa exactamente 6 dígitos numéricos                                        </p>`;
      return;
    }
                                                                                       resultado.innerHTML = `<p class="text-gray-600">🔍 Buscando...</p>`;           
    try {
      const res = await fetch(
        'https://petbio.siac2025.com/dir_controladores/buscar_mascota_publica.php',
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `codigo=${encodeURIComponent(codigo)}`
        }
      );

      if (!res.ok) throw new Error(`HTTP ${res.status}`);

      const data = await res.json();
      console.log("📦 Backend:", data);                                            
      if (!data.encontrada) {
        resultado.innerHTML = `<p>❌ Mascota no encontrada</p>`;
        return;
      }

      if (data.extraviada && data.id_extravio) {
        resultado.innerHTML = `
          <div class="p-4 border border-red-300 bg-red-50 rounded">
            <p class="font-bold text-red-700">🚨 Mascota extraviada</p>
            <p><strong>Nombre:</strong> ${data.nombre}</p>                                     <p><strong>Raza:</strong> ${data.raza}</p>
            <p><strong>Ciudad:</strong> ${data.ciudad}</p>
            <a class="inline-block mt-3 bg-red-600 text-white px-4 py-2 rounded"
               href="https://petbio.siac2025.com/dir_controladores/contactar_extravio.php?id_extravio=${data.id_extravio}">
              📩 Contactar tutor                                                               </a>
          </div>`;                                                                         return;
      }

      resultado.innerHTML = `
        <div class="p-4 border border-yellow-300 bg-yellow-50 rounded">
          <p class="font-bold text-yellow-700">
            ⚠️ Mascota sin reporte de extravío
          </p>
          <p><strong>Nombre:</strong> ${data.nombre}</p>
          <p><strong>Raza:</strong> ${data.raza}</p>
          <p><strong>Ciudad:</strong> ${data.ciudad}</p>
          <a class="inline-block mt-3 bg-yellow-600 text-white px-4 py-2 rounded"
             href="https://petbio.siac2025.com/dir_controladores/posible_caso_perdida_de_mascota.php?id_mascota=${data.id_mascota}">
            📨 Avisar posible extravío
          </a>
        </div>`;

    } catch (err) {
      console.error(err);
      resultado.innerHTML = `<p class="text-red-600">❌ Error del sistema</p>`;
    }
  });

});

