// פונקציה לייצוא טבלה ל-CSV
function exportTableToCSV(tableId, filename) {
    let table = document.getElementById(tableId);
    if (!table) {
        console.error(`Table with id ${tableId} not found`);
        return;
    }
    let rows = Array.from(table.rows);
    //קידוד הקובץ
    let csvContent = '\uFEFF'; 

    //שמירת התוכן כפורמט csv
    rows.forEach(row => {
        let cells = Array.from(row.cells);
        let rowContent = cells.map(cell => `"${cell.innerText}"`).join(',');
        csvContent += rowContent + '\n';
    });

    //והורדה שלו csv יצירת קובץ 
    let blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    let url = URL.createObjectURL(blob);
    let link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('exportBtn1')?.addEventListener('click', function () {
        exportTableToCSV('reportTable1', 'report1.csv');
    });
    document.getElementById('exportBtn2')?.addEventListener('click', function () {
        exportTableToCSV('reportTable2', 'report2.csv');
    });
    document.getElementById('exportBtn3')?.addEventListener('click', function () {
        exportTableToCSV('reportTable3', 'report3.csv');
    });
});
