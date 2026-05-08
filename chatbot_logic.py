import sys
import json
import urllib.request
import re

def get_db_data():
    try:
        url = "http://localhost/pntl-app/api_chatbot_db.php"
        req = urllib.request.Request(url)
        with urllib.request.urlopen(req) as response:
            data = json.loads(response.read().decode('utf-8'))
            return data
    except Exception as e:
        return None

def process_message(message):
    message = message.lower()
    data = get_db_data()
    
    if not data:
        return "Deskulpa, ha'u labele asesu baze de dadus agora daudaun."

    # Intent Matching
    if re.search(r'\b(edifisio|infraestrutura|predio)\b', message):
        if re.search(r'\b(munisipio|municipio|nebe mak|ida nebe|barak liu)\b', message) and not re.search(r'\b(hira|total)\b', message) and 'hira' not in message:
            muni_data = data.get('edifisio_por_munisipio', {})
            if muni_data:
                # Sort by total descending
                sorted_muni = sorted(muni_data.items(), key=lambda x: x[1], reverse=True)
                muni_list_str = ", ".join([f"{k} ({v})" for k, v in sorted_muni if v > 0])
                return f"Distribuisun edifísio tuir munisípiu: {muni_list_str}."
            return "Deskulpa, dadus munisípiu nian la disponivel."
        else:
            return f"Sistema PNTL-App agora daudaun rejista hamutuk edifísio PNTL {data.get('total_edifisio', 0)} iha territóriu laran."
            
    elif re.search(r'\b(munisipio|municipio)\b', message):
        return f"Iha munisípiu hamutuk {data.get('total_munisipio', 0)} ne'ebé rejista iha sistema."
        
    elif re.search(r'\b(postu|posto)\b', message):
        return f"Iha postu administrativu hamutuk {data.get('total_postu', 0)} ne'ebé rejista iha sistema."
        
    elif re.search(r'\b(suku|suco)\b', message):
        return f"Iha suku hamutuk {data.get('total_suco', 0)} ne'ebé rejista iha sistema."

    elif re.search(r'\b(saida mak|saida|oinsa)\b.*\b(aplikasaun|sistema|pntl)\b', message):
        return "Sistema PNTL-App mak plataforma dijitál ida ne'ebé uza hodi jere no mapea infraestrutura Polísia Nasionál Timor-Leste nian iha territóriu tomak."
        
    elif re.search(r'\b(obrigado|obrigadu|diak|parabens)\b', message):
        return "Nada! Se iha pergunta seluk konaba sistema PNTL-App, husu de'it."
        
    elif re.search(r'\b(bondia|botarde|boanoite|diak ka lae|ola|alo)\b', message):
        return "Olá! Ha'u mak Chatbot PNTL-App. Ita iha pergunta ruma konaba dadus infraestrutura PNTL?"
        
    else:
        return "Deskulpa, ha'u la kompriende ita-nia pergunta. Ha'u hatene de'it informasaun konaba total edifísio, munisípiu, postu, no suku iha sistema PNTL-App."

if __name__ == "__main__":
    if len(sys.argv) > 1:
        # Read the message from the first argument
        user_message = sys.argv[1]
    else:
        user_message = ""
        
    answer = process_message(user_message)
    
    # Output the JSON
    print(json.dumps({"response": answer}))
