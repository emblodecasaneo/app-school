<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reçu de Paiement #{{ $payment->id }}</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        .receipt-container {
            padding: 20px;
        }
        .receipt-header {
            background: #4f46e5;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .receipt-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .receipt-header p {
            margin: 5px 0 0;
            font-size: 16px;
        }
        .receipt-body {
            padding: 20px;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        .receipt-info-item {
            width: 30%;
            float: left;
            margin-right: 3%;
        }
        .receipt-info-item h3 {
            margin: 0 0 10px;
            color: #4f46e5;
            font-size: 16px;
        }
        .receipt-info-item p {
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }
        .receipt-details {
            margin-bottom: 20px;
            clear: both;
        }
        .receipt-details h3 {
            margin: 0 0 10px;
            color: #4f46e5;
            font-size: 16px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .receipt-table th, .receipt-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .receipt-table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #4b5563;
        }
        .receipt-footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #eee;
            color: #6b7280;
            font-size: 14px;
        }
        .receipt-total {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            font-weight: bold;
        }
        .receipt-total-item {
            padding: 10px 20px;
            background-color: #f9fafb;
            border-radius: 4px;
            text-align: right;
            float: right;
            width: 40%;
        }
        .receipt-total-item p {
            margin: 5px 0;
        }
        .receipt-total-item .total {
            color: #4f46e5;
            font-size: 18px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-partial {
            background-color: #fef3c7;
            color: #92400e;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>REÇU DE PAIEMENT</h1>
            <p>Année scolaire {{ $payment->schoolyear->school_year }}</p>
        </div>
        
        <div class="receipt-body">
            <div class="receipt-info clearfix">
                <div class="receipt-info-item">
                    <h3>INFORMATIONS DE L'ÉCOLE</h3>
                    <p><strong>Nom de l'école:</strong> École Primaire et Secondaire</p>
                    <p><strong>Adresse:</strong> 123 Rue de l'Éducation</p>
                    <p><strong>Téléphone:</strong> +123 456 789</p>
                    <p><strong>Email:</strong> contact@ecole.com</p>
                </div>
                
                <div class="receipt-info-item">
                    <h3>INFORMATIONS DE L'ÉLÈVE</h3>
                    <p><strong>Nom:</strong> {{ $payment->student->nom }} {{ $payment->student->prenom }}</p>
                    <p><strong>Matricule:</strong> {{ $payment->student->matricule }}</p>
                    <p><strong>Classe:</strong> {{ $payment->classe->libelle }}</p>
                </div>
                
                <div class="receipt-info-item">
                    <h3>DÉTAILS DU PAIEMENT</h3>
                    <p><strong>Reçu N°:</strong> #{{ $payment->id }}</p>
                    <p><strong>Date:</strong> {{ $payment->created_at->format('d/m/Y') }}</p>
                    <p><strong>Statut:</strong> 
                        <span class="status-badge {{ $payment->solvable == '1' ? 'status-paid' : 'status-partial' }}">
                            {{ $payment->solvable == '1' ? 'Payé' : 'Partiel' }}
                        </span>
                    </p>
                </div>
            </div>
            
            <div class="receipt-details">
                <h3>DÉTAILS DE LA TRANSACTION</h3>
                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Frais de scolarité - {{ $payment->classe->libelle }}</td>
                            <td>{{ number_format($payment->montant, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="receipt-total clearfix">
                    <div class="receipt-total-item">
                        <p>Montant payé: <strong>{{ number_format($payment->montant, 0, ',', ' ') }} FCFA</strong></p>
                        <p>Reste à payer: <strong>{{ number_format($payment->reste, 0, ',', ' ') }} FCFA</strong></p>
                        <p class="total">Total: <strong>{{ number_format($payment->montant + $payment->reste, 0, ',', ' ') }} FCFA</strong></p>
                    </div>
                </div>
            </div>
            
            <div class="receipt-footer">
                <p>Ce reçu est généré électroniquement et ne nécessite pas de signature.</p>
                <p>Pour toute question concernant ce paiement, veuillez contacter l'administration de l'école.</p>
            </div>
        </div>
    </div>
</body>
</html> 