<style>
    .print {
        margin: 10mm 15mm 10mm 15mm;
    }

    table {
        border: none;
        width: 100%;
        margin: 0;
        padding: 0;
        border-collapse: collapse;
    }

    .table-cust {
        border-left: 1px #000 solid;
        border-right: 1px #000 solid;
        border-top: 1px #000 solid;
        background: rgb(235, 235, 235)
    }

    table tr {
        border: 0px;
        padding: 5px;
    }

    table th,
    table td {
        padding: 5px;
        font-size: 9pt;
        vertical-align: top;
    }

    table th {
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .border-btm {
        border-bottom: 1px solid #000;
    }

    .border-tp {
        border-top: 1px solid #000;
    }

    .border-cust {
        border: 1px solid #000;
    }

    .border-cust th {
        border: 1px solid #000;
    }

    .border-lr td {
        border-right: 1px solid #000;
        border-left: 1px solid #000;
    }

    .border-rl {
        border-right: 1px solid #000;
        border-left: 1px solid #000;
    }



    .center {
        text-align: center;
    }

    .right {
        text-align: right;
    }

    .left {
        text-align: left;
    }

    .text-red {
        color: red;
    }

    .overline {
        text-decoration: overline;
    }

    h1,
    h2,
    h3 {
        margin: 0;
    }

    .report-title {
        margin-bottom: 10px;
    }
</style>

<style>
    table.report-container {
        page-break-after: always;
    }

    thead.report-header {
        display: table-header-group;
    }

    tfoot.report-footer {
        display: table-footer-group;
    }
</style>

<style type="text/css" media="print">
    @page {
        margin: 2mm;
        size: A4 portrait;
    }

    @media print {

        html,
        body {
            width: 210mm;
            height: 148mm;
        }
    }
</style>
