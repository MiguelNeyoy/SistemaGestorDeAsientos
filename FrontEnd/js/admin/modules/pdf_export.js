import { fetchEscaneados } from './api.js?v=1';
import { toast } from '../../core/toast.js?v=1';

/**
 * Solicita los alumnos escaneados al servidor y genera un PDF con html2pdf.js
 * @param {string} evento - 'li' o 'lisi'
 * @param {string} label - Nombre para mostrar en toasts
 */
export async function exportarPdf(evento, label) {
    toast.info(`Generando PDF de ${label}...`);

    try {
        // 1. Pedir datos al servidor (las peticiones en el archivo correspondiente)
        const result = await fetchEscaneados(evento);

        if (!result.success || !result.data) {
            toast.error(result.message || 'No se pudieron obtener los datos.');
            return;
        }

        const { evento: tituloEvento, total, alumnos } = result.data;

        // 2. Separar alumnos por turno
        const matutino = alumnos.filter(a => ['M', '1'].includes(a.turno?.toUpperCase()));
        const vespertino = alumnos.filter(a => !['M', '1'].includes(a.turno?.toUpperCase()));

        // 3. Construir el HTML del reporte y pasarlo como string
        const html = construirHtml(tituloEvento, total, matutino, vespertino);

        // 4. Generar PDF con html2pdf.js desde string (crea su propio iframe interno)
        await window.html2pdf().set({
            margin:       [10, 10, 10, 10],
            filename:     `Lista_Asistencia_${evento.toUpperCase()}.pdf`,
            image:        { type: 'jpeg', quality: 0.95 },
            html2canvas:  { scale: 2, useCORS: true, logging: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).from(html, 'string').save();

        toast.success('PDF descargado correctamente.');

    } catch (error) {
        console.error('Error al exportar PDF:', error);
        toast.error('Ocurrió un error al generar el PDF.');
    }
}

/**
 * Construye el HTML de la lista de asistencia
 */
function construirHtml(tituloEvento, total, matutino, vespertino) {
    const fecha = new Date().toLocaleString('es-MX', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });

    const S = (s) => ` style="${s}"`;
    const headerAttrs = 'text-align:center;border-bottom:2px solid #111167;padding-bottom:10px;margin-bottom:15px';
    const h1Attrs = 'color:#111167;font-size:18px;text-transform:uppercase;margin:0 0 4px 0;font-family:Helvetica,Arial,sans-serif';
    const pAttrs = 'color:#555;margin:2px 0;font-size:11px;font-family:Helvetica,Arial,sans-serif';
    const turnoAttrs = 'background:#f4f4f4;padding:6px 10px;border-left:4px solid #1565c0;margin:15px 0 8px 0;font-size:13px;font-weight:bold;font-family:Helvetica,Arial,sans-serif';
    const tableAttrs = 'width:100%;border-collapse:collapse;margin-bottom:15px';
    const thAttrs = 'padding:5px 8px;border:1px solid #ddd;background:#f8f9fa;font-weight:bold;font-family:Helvetica,Arial,sans-serif;font-size:11px;color:#333';
    const tdAttrs = 'padding:5px 8px;border:1px solid #ddd;font-family:Helvetica,Arial,sans-serif;font-size:11px;color:#333';
    const emptyAttrs = 'text-align:center;color:#888;padding:15px;font-style:italic;font-family:Helvetica,Arial,sans-serif;font-size:11px';
    const footerAttrs = 'margin-top:20px;text-align:right;font-weight:bold;font-size:13px;font-family:Helvetica,Arial,sans-serif;color:#333';

    const thRow = (w, txt, align) =>
        `<th width="${w}"${S(`${thAttrs};text-align:${align || 'left'}`)}>${txt}</th>`;

    const generarTabla = (alumnos) => {
        if (alumnos.length === 0) {
            return `
                <table${S(tableAttrs)}>
                    <thead>
                        <tr>
                            ${thRow('6%', '#', 'center')}
                            ${thRow('50%', 'Nombre Completo')}
                            ${thRow('22%', 'Núm. Cuenta', 'center')}
                            ${thRow('22%', 'Asiento', 'center')}
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4"${S(emptyAttrs)}>
                                No hay alumnos presentes registrados en este turno.
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;
        }

        const filas = alumnos.map((a, i) => `
            <tr>
                <td${S(`${tdAttrs};text-align:center`)}>${i + 1}</td>
                <td${S(tdAttrs)}>${(a.apellido || '').trim()} ${(a.nombre || '').trim()}</td>
                <td${S(`${tdAttrs};text-align:center`)}>${a.numCuenta}</td>
                <td${S(`${tdAttrs};text-align:center`)}>${a.letra}-${a.numero}</td>
            </tr>
        `).join('');

        return `
            <table${S(tableAttrs)}>
                <thead>
                    <tr>
                        ${thRow('6%', '#', 'center')}
                        ${thRow('50%', 'Nombre Completo')}
                        ${thRow('22%', 'Núm. Cuenta', 'center')}
                        ${thRow('22%', 'Asiento', 'center')}
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        `;
    };

    return `
        <div${S(headerAttrs)}>
            <h1${S(h1Attrs)}>Lista de Alumnos Presentes</h1>
            <p${S(pAttrs)}><strong>Evento:</strong> ${tituloEvento}</p>
            <p${S(pAttrs)}><strong>Generado:</strong> ${fecha}</p>
        </div>

        <div${S(turnoAttrs)}>Turno Matutino (${matutino.length} alumnos)</div>
        ${generarTabla(matutino)}

        <div${S(turnoAttrs)}>Turno Vespertino (${vespertino.length} alumnos)</div>
        ${generarTabla(vespertino)}

        <div${S(footerAttrs)}>
            Total Presentes: ${total} alumnos
        </div>
    `;
}
