# Free NLP Libraries Guide for Enhanced AI Responses

## 🚀 **Overview**

This guide provides free NLP libraries and tools to enhance your AI system's natural language understanding, including Swahili support.

## 📚 **Free NLP Libraries**

### 1. **Python NLP Libraries (For Advanced Processing)**

#### **spaCy** (Free for Basic Use)
```bash
pip install spacy
python -m spacy download en_core_web_sm
python -m spacy download xx_ent_wiki_sm  # Multi-language
```

**Features:**
- Named Entity Recognition (NER)
- Part-of-Speech Tagging
- Dependency Parsing
- Language Detection
- Custom Training

**Usage:**
```python
import spacy

nlp = spacy.load("en_core_web_sm")
doc = nlp("Hello, how are you today?")
for token in doc:
    print(token.text, token.pos_, token.dep_)
```

#### **NLTK** (Completely Free)
```bash
pip install nltk
```

**Features:**
- Tokenization
- Stemming
- Lemmatization
- Sentiment Analysis
- Language Detection

**Usage:**
```python
import nltk
from nltk.sentiment import SentimentIntensityAnalyzer

sia = SentimentIntensityAnalyzer()
sentiment = sia.polarity_scores("I love this product!")
```

#### **TextBlob** (Free)
```bash
pip install textblob
```

**Features:**
- Sentiment Analysis
- Language Detection
- Translation
- Part-of-Speech Tagging

**Usage:**
```python
from textblob import TextBlob

blob = TextBlob("Jambo! Habari yako?")
print(blob.detect_language())  # 'sw'
print(blob.sentiment.polarity)
```

### 2. **Swahili-Specific Libraries**

#### **SwahiliNLP** (Community Project)
```bash
pip install swahilinlp
```

**Features:**
- Swahili Tokenization
- Swahili Stemming
- Swahili Sentiment Analysis

#### **Hugging Face Transformers** (Free)
```bash
pip install transformers
```

**Features:**
- Pre-trained models for multiple languages
- Translation models
- Sentiment analysis
- Text classification

**Usage:**
```python
from transformers import pipeline

# Sentiment analysis
classifier = pipeline("sentiment-analysis")
result = classifier("I love this business!")

# Translation
translator = pipeline("translation", model="Helsinki-NLP/opus-mt-en-sw")
translated = translator("Hello, how are you?")
```

### 3. **Language Detection Libraries**

#### **langdetect** (Free)
```bash
pip install langdetect
```

**Usage:**
```python
from langdetect import detect

language = detect("Jambo! Habari yako?")  # 'sw'
```

#### **polyglot** (Free)
```bash
pip install polyglot
```

**Features:**
- Language detection
- Named entity recognition
- Sentiment analysis
- Part-of-speech tagging

## 🔧 **Integration with Your System**

### 1. **Enhanced Language Detection**

```php
// In your EnhancedAIService.php
protected function detectLanguageEnhanced($message)
{
    // Call Python script for advanced detection
    $command = "python3 language_detector.py " . escapeshellarg($message);
    $result = shell_exec($command);
    return trim($result) ?: 'en';
}
```

### 2. **Python Language Detector Script**

```python
# language_detector.py
import sys
from langdetect import detect
from textblob import TextBlob

def detect_language(text):
    try:
        # Try langdetect first
        lang = detect(text)
        
        # Double-check with TextBlob
        blob = TextBlob(text)
        if blob.detect_language() == 'sw':
            return 'sw'
        
        return lang
    except:
        return 'en'

if __name__ == "__main__":
    text = sys.argv[1]
    print(detect_language(text))
```

### 3. **Enhanced Sentiment Analysis**

```python
# sentiment_analyzer.py
import sys
from textblob import TextBlob
from nltk.sentiment import SentimentIntensityAnalyzer
import nltk

def analyze_sentiment(text, language):
    if language == 'sw':
        # Swahili-specific sentiment analysis
        positive_words = ['nzuri', 'bora', 'ajabu', 'mzuri', 'kamili', 'furaha']
        negative_words = ['mbaya', 'huzuni', 'kutisha', 'maskini', 'hasara']
        
        text_lower = text.lower()
        positive_count = sum(1 for word in positive_words if word in text_lower)
        negative_count = sum(1 for word in negative_words if word in text_lower)
        
        if positive_count > negative_count:
            return 'positive'
        elif negative_count > positive_count:
            return 'negative'
        else:
            return 'neutral'
    else:
        # English sentiment analysis
        sia = SentimentIntensityAnalyzer()
        scores = sia.polarity_scores(text)
        
        if scores['compound'] >= 0.05:
            return 'positive'
        elif scores['compound'] <= -0.05:
            return 'negative'
        else:
            return 'neutral'

if __name__ == "__main__":
    text = sys.argv[1]
    language = sys.argv[2]
    print(analyze_sentiment(text, language))
```

## 🌐 **Free Translation APIs**

### 1. **Google Translate API** (Free Tier)
```python
from googletrans import Translator

translator = Translator()
result = translator.translate("Hello, how are you?", dest='sw')
print(result.text)  # "Jambo, habari yako?"
```

### 2. **LibreTranslate** (Free)
```python
import requests

def translate_text(text, source_lang, target_lang):
    url = "https://libretranslate.com/translate"
    data = {
        "q": text,
        "source": source_lang,
        "target": target_lang
    }
    response = requests.post(url, data=data)
    return response.json()["translatedText"]
```

## 📊 **Enhanced Intent Classification**

### 1. **Keyword-Based Classification**

```php
protected function classifyIntentEnhanced($message, $language)
{
    // Call Python script for advanced classification
    $command = "python3 intent_classifier.py " . escapeshellarg($message) . " " . escapeshellarg($language);
    $result = shell_exec($command);
    return trim($result) ?: 'general';
}
```

### 2. **Python Intent Classifier**

```python
# intent_classifier.py
import sys
import re
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.naive_bayes import MultinomialNB
import pickle

def classify_intent(text, language):
    # Load pre-trained model or use rule-based approach
    if language == 'sw':
        intents = {
            'greeting': ['jambo', 'habari', 'salamu', 'hujambo'],
            'sales_analysis': ['mauzo', 'mapato', 'faida', 'pesa', 'biashara'],
            'market_trends': ['soko', 'mwelekeo', 'mpinzani', 'sekta'],
            'recommendations': ['pendekeza', 'shauri', 'ushauri', 'lazima'],
            'help': ['msaada', 'nini', 'jinsi gani', 'nieleze']
        }
    else:
        intents = {
            'greeting': ['hello', 'hi', 'hey', 'good morning'],
            'sales_analysis': ['sales', 'revenue', 'profit', 'income', 'earnings'],
            'market_trends': ['market', 'trend', 'competitor', 'industry'],
            'recommendations': ['recommend', 'suggestion', 'advice', 'should'],
            'help': ['help', 'what can', 'how can', 'tell me']
        }
    
    text_lower = text.lower()
    best_intent = 'general'
    best_score = 0
    
    for intent, keywords in intents.items():
        score = sum(1 for keyword in keywords if keyword in text_lower)
        if score > best_score:
            best_score = score
            best_intent = intent
    
    return best_intent

if __name__ == "__main__":
    text = sys.argv[1]
    language = sys.argv[2]
    print(classify_intent(text, language))
```

## 🚀 **cPanel Integration**

### 1. **Cron Job Setup**

In cPanel, set up cron jobs to run your continuous learning:

```bash
# Hourly learning
0 * * * * /usr/bin/php /home/username/public_html/cpanel_continuous_learning.php

# Daily deep learning (2 AM)
0 2 * * * /usr/bin/php /home/username/public_html/daily_learning.php

# Weekly comprehensive analysis (Sundays 3 AM)
0 3 * * 0 /usr/bin/php /home/username/public_html/weekly_analysis.php
```

### 2. **Python Script Execution**

Create a simple PHP wrapper to call Python scripts:

```php
// In your AI service
protected function callPythonScript($script, $args)
{
    $command = "python3 " . escapeshellarg($script) . " " . implode(" ", array_map('escapeshellarg', $args));
    return shell_exec($command);
}
```

## 📈 **Performance Optimization**

### 1. **Caching Responses**

```php
protected function getCachedResponse($message, $language)
{
    $cacheKey = md5($message . $language);
    return Cache::remember($cacheKey, 3600, function() use ($message, $language) {
        return $this->processEnhancedMessage($message, $language);
    });
}
```

### 2. **Batch Processing**

```php
protected function processBatchMessages($messages)
{
    $results = [];
    foreach ($messages as $message) {
        $results[] = $this->processEnhancedMessage($message);
    }
    return $results;
}
```

## 🔒 **Security Considerations**

1. **Input Sanitization**: Always sanitize user input
2. **Rate Limiting**: Implement rate limiting for API calls
3. **Error Handling**: Proper error handling for external API calls
4. **Logging**: Log all AI interactions for debugging

## 📞 **Support and Resources**

### Free NLP Resources:
- **NLTK Documentation**: https://www.nltk.org/
- **spaCy Documentation**: https://spacy.io/
- **Hugging Face**: https://huggingface.co/
- **Swahili NLP Community**: GitHub repositories

### Testing Your Implementation:
```bash
# Test language detection
python3 language_detector.py "Jambo! Habari yako?"

# Test sentiment analysis
python3 sentiment_analyzer.py "I love this business!" en

# Test intent classification
python3 intent_classifier.py "How are my sales performing?" en
```

---

**Note**: These free libraries provide excellent NLP capabilities that can significantly enhance your AI system's understanding and response quality, especially for Swahili language support.

