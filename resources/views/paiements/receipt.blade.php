<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement #{{ $payment->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }
        .receipt-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
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
            flex: 1;
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
        }
        .receipt-total-item p {
            margin: 5px 0;
        }
        .receipt-total-item .total {
            color: #4f46e5;
            font-size: 18px;
        }
        .receipt-actions {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .receipt-actions button, .receipt-actions a {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }
        .receipt-actions button:hover, .receipt-actions a:hover {
            background-color: #4338ca;
        }
        .receipt-actions .print-button {
            background-color: #4f46e5;
        }
        .receipt-actions .pdf-button {
            background-color: #10b981;
        }
        .receipt-actions .pdf-button:hover {
            background-color: #059669;
        }
        .receipt-actions .back-button {
            background-color: #6b7280;
        }
        .receipt-actions .back-button:hover {
            background-color: #4b5563;
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
        @media print {
            body {
                background-color: white;
            }
            .receipt-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }
            .receipt-actions {
                display: none;
            }
            .no-print {
                display: none;
            }
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
            <div class="receipt-info">
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
                
                <div class="receipt-total">
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
            
            <div class="receipt-actions no-print">
                <button onclick="window.print()" class="print-button">Imprimer le reçu</button>
                <a href="{{ route('paiements.receipt-pdf', ['payment' => $payment->id]) }}" class="pdf-button" target="_blank">Télécharger en PDF</a>
                <button onclick="window.history.back()" class="back-button">Retour</button>
            </div>
        </div>
    </div>
</body>
</html> 