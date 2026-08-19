export default class Modal {
    mostrarModal(titulo, mensaje, tipo = 'advertencia') {
        const modal = document.createElement('div');
        const modalContent = document.createElement('div');
        const modalHeader = document.createElement('div');
        const icono = document.createElement('div');
        const tituloContainer = document.createElement('div');
        const modalBody = document.createElement('div');
        const modalFooter = document.createElement('div');
        const closeButton = document.createElement('button');
        const okButton = document.createElement('button');

        // Estilos del modal principal
        Object.assign(modal.style, {
            position: 'fixed',
            left: '0',
            top: '0',
            width: '100%',
            height: '100%',
            backgroundColor: 'rgba(0,0,0,0.7)',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            zIndex: '1000',
            fontFamily: '"Segoe UI", Roboto, "Helvetica Neue", sans-serif',
            backdropFilter: 'blur(3px)'
        });

        // Estilos del contenido del modal
        Object.assign(modalContent.style, {
            backgroundColor: '#1a1a1a',
            padding: '25px',
            borderRadius: '8px',
            width: '85%',
            maxWidth: '450px',
            boxShadow: '0 10px 25px rgba(0,0,0,0.5)',
            border: '1px solid #ff2e2e',
            color: '#e0e0e0',
            animation: 'fadeIn 0.3s ease-out'
        });

        // Estilos del encabezado
        modalHeader.style.display = 'flex';
        modalHeader.style.alignItems = 'center';
        modalHeader.style.marginBottom = '20px';
        modalHeader.style.paddingBottom = '15px';
        modalHeader.style.borderBottom = '1px solid #ff2e2e';

        // Icono (puedes reemplazar con un SVG o imagen)
        icono.innerHTML = '⚠️'; // O usar un icono personalizado
        icono.style.marginRight = '15px';
        icono.style.fontSize = '1.8em';

        // Contenedor del título
        tituloContainer.innerHTML = `
            <div style="font-size: 1.4em; font-weight: 600; color: ${tipo === 'advertencia' ? '#ff2e2e' : '#e0e0e0'};">
                TrakioInjector <span style="color: #e0e0e0;">Dice</span>
            </div>
            <div style="font-size: 1.1em; margin-top: 5px; color: #b0b0b0;">${titulo}</div>
        `;

        // Cuerpo del modal
        modalBody.style.marginBottom = '25px';
        modalBody.style.padding = '15px';
        modalBody.style.backgroundColor = '#252525';
        modalBody.style.borderRadius = '4px';
        modalBody.style.borderLeft = `3px solid ${tipo === 'advertencia' ? '#ff2e2e' : '#4CAF50'}`;
        modalBody.textContent = mensaje;
        modalBody.style.lineHeight = '1.5';

        // Pie del modal
        modalFooter.style.display = 'flex';
        modalFooter.style.justifyContent = 'flex-end';
        modalFooter.style.gap = '12px';

        // Botón de cerrar
        closeButton.textContent = 'Cerrar';
        Object.assign(closeButton.style, {
            padding: '10px 20px',
            backgroundColor: 'transparent',
            color: '#ff2e2e',
            border: '1px solid #ff2e2e',
            borderRadius: '4px',
            cursor: 'pointer',
            fontWeight: '500',
            transition: 'all 0.3s ease',
            fontSize: '0.9em'
        });

        // Botón de aceptar
        okButton.textContent = 'Aceptar';
        Object.assign(okButton.style, {
            padding: '10px 20px',
            backgroundColor: '#ff2e2e',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer',
            fontWeight: '500',
            transition: 'all 0.3s ease',
            fontSize: '0.9em'
        });

        // Efectos hover
        closeButton.onmouseenter = () => closeButton.style.backgroundColor = 'rgba(255, 46, 46, 0.1)';
        closeButton.onmouseleave = () => closeButton.style.backgroundColor = 'transparent';
        okButton.onmouseenter = () => okButton.style.backgroundColor = '#ff1a1a';
        okButton.onmouseleave = () => okButton.style.backgroundColor = '#ff2e2e';

        // Funcionalidad de cierre
        closeButton.onclick = okButton.onclick = () => {
            modal.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => document.body.removeChild(modal), 300);
        };

        modal.onclick = (e) => {
            if (e.target === modal) {
                modal.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => document.body.removeChild(modal), 300);
            }
        };

        // Añadir animaciones CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes fadeOut {
                from { opacity: 1; transform: translateY(0); }
                to { opacity: 0; transform: translateY(-20px); }
            }
        `;
        document.head.appendChild(style);

        // Ensamblar el modal
        modalHeader.appendChild(icono);
        modalHeader.appendChild(tituloContainer);
        modalFooter.appendChild(okButton);
        modalFooter.appendChild(closeButton);
        modalContent.appendChild(modalHeader);
        modalContent.appendChild(modalBody);
        modalContent.appendChild(modalFooter);
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
    }
}