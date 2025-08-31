function parseCurrency(value) {
    return Number(String(value).replace(/\D/g, ''))
}

function generateCurrency(number) {
    return 'Rp ' + Number(number).toLocaleString('id-id')
}
