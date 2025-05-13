function calculateTotal() {
    const pricing = {
        washAndFold: 180,
        drying: 30,
        ironing: 20,
        deepDrying: 150,
        stainRemoval: 30,
        fabricSoftening: 20,
    };

    let totalCost = 0;
    let breakdown = '';

    const washAndFoldQty = parseInt(document.getElementById('washAndFold').value) || 0;
    const dryingQty = parseInt(document.getElementById('drying').value) || 0;
    const ironingQty = parseInt(document.getElementById('ironing').value) || 0;
    const deepDryingQty = parseInt(document.getElementById('deepDrying').value) || 0;
    const stainRemovalQty = parseInt(document.getElementById('stainRemoval').value) || 0;
    const fabricSofteningQty = parseInt(document.getElementById('fabricSoftening').value) || 0;

    if (washAndFoldQty > 0) {
        totalCost += washAndFoldQty * pricing.washAndFold;
        breakdown += `Wash and Fold: ${washAndFoldQty} x ${pricing.washAndFold} = ${washAndFoldQty * pricing.washAndFold} PHP<br>`;
    }
    if (dryingQty > 0) {
        totalCost += dryingQty * pricing.drying;
        breakdown += `Drying: ${dryingQty} x ${pricing.drying} = ${dryingQty * pricing.drying} PHP<br>`;
    }
    if (ironingQty > 0) {
        totalCost += ironingQty * pricing.ironing;
        breakdown += `Ironing: ${ironingQty} x ${pricing.ironing} = ${ironingQty * pricing.ironing} PHP<br>`;
    }
    if (deepDryingQty > 0) {
        totalCost += deepDryingQty * pricing.deepDrying;
        breakdown += `Deep Drying: ${deepDryingQty} x ${pricing.deepDrying} = ${deepDryingQty * pricing.deepDrying} PHP<br>`;
    }
    if (stainRemovalQty > 0) {
        totalCost += stainRemovalQty * pricing.stainRemoval;
        breakdown += `Stain Removal: ${stainRemovalQty} x ${pricing.stainRemoval} = ${stainRemovalQty * pricing.stainRemoval} PHP<br>`;
    }
    if (fabricSofteningQty > 0) {
        totalCost += fabricSofteningQty * pricing.fabricSoftening;
        breakdown += `Fabric Softening: ${fabricSofteningQty} x ${pricing.fabricSoftening} = ${fabricSofteningQty * pricing.fabricSoftening} PHP<br>`;
    }

    document.getElementById('breakdown').innerHTML = breakdown;
    document.getElementById('totalCost').innerHTML = `Total Cost: ${totalCost} PHP`;
    document.getElementById('submitOrder').style.display = totalCost > 0 ? 'block' : 'none';
}