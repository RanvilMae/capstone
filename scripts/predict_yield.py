import sys
import json
import os
import torch
from transformers import AutoTokenizer, AutoModelForSequenceClassification, logging

# Suppress Hugging Face download/loading warnings & info logs
logging.set_verbosity_error()
os.environ["TF_CPP_MIN_LOG_LEVEL"] = "3"

MODEL_PATH = './storage/app/bert_latex_model'

LABELS = [
    "Low Yield - Poor Quality",
    "Standard Yield - Normal Quality",
    "High Yield - Premium Quality"
]

def load_model_and_predict(plot_code, volume_kg, drc_percent):
    try:
        tokenizer = AutoTokenizer.from_pretrained(MODEL_PATH)
        model = AutoModelForSequenceClassification.from_pretrained(MODEL_PATH)
        model.eval()

        input_text = f"Plot: {plot_code}, Fresh Weight: {volume_kg} kg, DRC: {drc_percent}%"
        inputs = tokenizer(input_text, return_tensors="pt", padding=True, truncation=True, max_length=128)

        with torch.no_grad():
            outputs = model(**inputs)
            predicted_class_id = torch.argmax(outputs.logits, dim=1).item()

        return {
            "status": "success",
            "prediction": LABELS[predicted_class_id],
            "class_id": predicted_class_id
        }

    except Exception as e:
        return {
            "status": "error",
            "message": str(e)
        }

if __name__ == "__main__":
    if len(sys.argv) >= 4:
        plot = sys.argv[1]
        vol = float(sys.argv[2])
        drc = float(sys.argv[3])

        result = load_model_and_predict(plot, vol, drc)
        print(json.dumps(result))
    else:
        print(json.dumps({
            "status": "error", 
            "message": "Missing arguments"
        }))