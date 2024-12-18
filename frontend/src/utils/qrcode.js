import QRCode from 'qrcode'

const generateOrderQRCode = async (order) => {
    // Dados que queremos incluir no QR Code
    const payload = {
        orderId: order.id,
        timestamp: order.created_at,
        total: order.total,
        cnpj: '12.345.678/0001-90',
        items: order.items.map(item => ({
            id: item.id,
            quantity: item.quantity,
            price: item.price
        }))
    }

    // Gerar o QR Code como Data URL
    try {
        const qrCodeDataUrl = await QRCode.toDataURL(JSON.stringify(payload), {
            width: 128,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#ffffff'
            }
        })
        return qrCodeDataUrl
    } catch (err) {
        console.error('Erro ao gerar QR Code:', err)
        return null
    }
}

export { generateOrderQRCode }
