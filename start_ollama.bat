@echo off
echo Starting Ollama for NutriMind AI features...
wsl -e bash -c "pkill -9 ollama 2>/dev/null; sleep 1; OLLAMA_HOST=0.0.0.0 OLLAMA_ORIGINS='*' ollama serve"
pause
