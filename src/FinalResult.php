<?php
class FinalResult {
    function results($final) {
        // if using /r in file in rare cases
        ini_set('auto_detect_line_endings',TRUE);
        // Open CSV file
        $document = fopen($final, "r");
        $document_data = fgetcsv($document);
        $records_data = [];
        // $document_array_length = 16; 
        while(!feof($document)) {
            $record_array = fgetcsv($document);
            $records_data[] = $this->documentArrayCreation($document_data, $record_array);
        }
        $records_data = array_filter($records_data);

        ini_set('auto_detect_line_endings',FALSE);

        // Close CSV file
        fclose($document);
        return [
            "filename" => basename($final),
            "document" => $document,
            "failure_code" => $document_data[1],
            "failure_message" => $document_data[2],
            "records" => $records_data
        ];
    }

    public function documentArrayCreation ($document_data, $record_array)
    {
        $amount_data = !$record_array[8] || $record_array[8] == "0" ? 0 : (float) $record_array[8];
        $bank_number = !$record_array[6] ? "Bank account number missing" : (int) $record_array[6];
        $bank_b_code = !$record_array[2] ? "Bank branch code missing" : $record_array[2];
        $e2e_id = !$record_array[10] && !$record_array[11] ? "End to end id missing" : $record_array[10] . $record_array[11];
        return [
            "amount" => [
                "currency" => $document_data[0],
                "subunits" => (int) ($amount_data * 100)
            ],
            "bank_account_name" => str_replace(" ", "_", strtolower($record_array[7])),
            "bank_account_number" => $bank_number,
            "bank_branch_code" => $bank_b_code,
            "bank_code" => $record_array[0],
            "end_to_end_id" => $e2e_id,
        ];
    }
}

?>
