#!/usr/bin/env python3
"""
AI Content Enhancer and Generator
Enhances existing content and generates new content for various business needs
"""

import sys
import json
import re
import random
from datetime import datetime

class ContentEnhancer:
    def __init__(self):
        """Initialize the content enhancer"""
        self.content_templates = {
            'product_description': {
                'structure': [
                    'compelling_headline',
                    'key_features',
                    'benefits',
                    'technical_specs',
                    'call_to_action'
                ],
                'tone_modifiers': {
                    'professional': ['premium', 'expert', 'quality', 'reliable'],
                    'casual': ['awesome', 'great', 'amazing', 'perfect'],
                    'technical': ['advanced', 'sophisticated', 'innovative', 'cutting-edge']
                }
            },
            'service_description': {
                'structure': [
                    'service_overview',
                    'process_explanation',
                    'benefits_list',
                    'expertise_highlight',
                    'contact_info'
                ],
                'tone_modifiers': {
                    'professional': ['expert', 'professional', 'reliable', 'trusted'],
                    'friendly': ['helpful', 'caring', 'supportive', 'dedicated'],
                    'authoritative': ['leading', 'premier', 'exclusive', 'superior']
                }
            },
            'business_description': {
                'structure': [
                    'company_intro',
                    'mission_statement',
                    'core_values',
                    'expertise_areas',
                    'contact_details'
                ],
                'tone_modifiers': {
                    'professional': ['established', 'reputable', 'trusted', 'expert'],
                    'enthusiastic': ['passionate', 'dedicated', 'committed', 'excited'],
                    'formal': ['distinguished', 'esteemed', 'prestigious', 'renowned']
                }
            }
        }
        
        self.enhancement_techniques = {
            'grammar_improvement': self._improve_grammar,
            'vocabulary_enhancement': self._enhance_vocabulary,
            'structure_optimization': self._optimize_structure,
            'tone_adjustment': self._adjust_tone,
            'clarity_improvement': self._improve_clarity
        }
    
    def enhance_content(self, original_text, content_type='description', tone='professional'):
        """Enhance existing content with AI improvements"""
        try:
            enhanced_text = original_text
            improvements = []
            
            # Apply enhancement techniques
            for technique_name, technique_func in self.enhancement_techniques.items():
                enhanced_text, improvement = technique_func(enhanced_text, content_type, tone)
                if improvement:
                    improvements.append(improvement)
            
            # Apply content type specific enhancements
            if content_type in self.content_templates:
                enhanced_text, structure_improvement = self._apply_content_structure(
                    enhanced_text, content_type, tone
                )
                if structure_improvement:
                    improvements.append(structure_improvement)
            
            return {
                'enhanced_content': enhanced_text,
                'improvements': improvements,
                'word_count': len(enhanced_text.split()),
                'original_length': len(original_text.split()),
                'improvement_percentage': self._calculate_improvement_percentage(original_text, enhanced_text)
            }
            
        except Exception as e:
            return {
                'error': f'Enhancement failed: {str(e)}',
                'original_content': original_text
            }
    
    def generate_content(self, keywords, content_type='description', tone='professional', length='medium'):
        """Generate new content from keywords"""
        try:
            # Parse keywords
            keyword_list = [kw.strip() for kw in keywords.split(',')]
            
            # Generate content based on type and tone
            if content_type in self.content_templates:
                generated_content = self._generate_structured_content(
                    keyword_list, content_type, tone, length
                )
            else:
                generated_content = self._generate_generic_content(
                    keyword_list, tone, length
                )
            
            return {
                'generated_content': generated_content,
                'keywords_used': keyword_list,
                'word_count': len(generated_content.split()),
                'content_type': content_type,
                'tone': tone,
                'length': length
            }
            
        except Exception as e:
            return {
                'error': f'Generation failed: {str(e)}',
                'keywords': keywords
            }
    
    def optimize_for_seo(self, content, target_keywords):
        """Optimize content for SEO"""
        try:
            optimized_content = content
            seo_score = 0
            keyword_density = {}
            suggestions = []
            
            # Calculate keyword density
            for keyword in target_keywords:
                density = self._calculate_keyword_density(content, keyword)
                keyword_density[keyword] = density
                
                # Suggest improvements based on density
                if density < 0.01:  # Less than 1%
                    suggestions.append(f"Increase usage of '{keyword}'")
                elif density > 0.05:  # More than 5%
                    suggestions.append(f"Reduce overuse of '{keyword}'")
            
            # Optimize content structure
            optimized_content = self._add_seo_elements(optimized_content, target_keywords)
            
            # Calculate SEO score
            seo_score = self._calculate_seo_score(optimized_content, target_keywords)
            
            return {
                'optimized_content': optimized_content,
                'seo_score': seo_score,
                'keyword_density': keyword_density,
                'suggestions': suggestions
            }
            
        except Exception as e:
            return {
                'error': f'SEO optimization failed: {str(e)}',
                'original_content': content
            }
    
    def generate_variations(self, base_content, content_type='description', count=3):
        """Generate multiple content variations"""
        try:
            variations = []
            
            for i in range(count):
                variation = self._create_variation(base_content, content_type, i)
                variations.append({
                    'id': i + 1,
                    'content': variation,
                    'word_count': len(variation.split()),
                    'style': self._get_variation_style(i)
                })
            
            return {
                'variations': variations,
                'count': len(variations),
                'base_content': base_content
            }
            
        except Exception as e:
            return {
                'error': f'Variation generation failed: {str(e)}',
                'base_content': base_content
            }
    
    def _improve_grammar(self, text, content_type, tone):
        """Improve grammar and sentence structure"""
        improvements = []
        
        # Fix common grammar issues
        text = re.sub(r'\b(i)\b', 'I', text, flags=re.IGNORECASE)
        text = re.sub(r'\s+', ' ', text)  # Remove extra spaces
        text = re.sub(r'([.!?])\s*([a-z])', lambda m: m.group(1) + ' ' + m.group(2).upper(), text)
        
        improvements.append('Grammar and punctuation improved')
        return text, improvements
    
    def _enhance_vocabulary(self, text, content_type, tone):
        """Enhance vocabulary based on tone"""
        improvements = []
        
        # Get tone-specific modifiers
        if content_type in self.content_templates and tone in self.content_templates[content_type]['tone_modifiers']:
            modifiers = self.content_templates[content_type]['tone_modifiers'][tone]
            
            # Add tone-appropriate words
            for modifier in modifiers:
                if modifier not in text.lower():
                    # Insert modifier in appropriate places
                    text = self._insert_modifier(text, modifier)
                    improvements.append(f'Added {tone} vocabulary')
                    break
        
        return text, improvements
    
    def _optimize_structure(self, text, content_type, tone):
        """Optimize content structure"""
        improvements = []
        
        # Ensure proper paragraph structure
        sentences = text.split('. ')
        if len(sentences) > 1:
            # Create better paragraph breaks
            text = self._create_paragraphs(sentences)
            improvements.append('Improved paragraph structure')
        
        return text, improvements
    
    def _adjust_tone(self, text, content_type, tone):
        """Adjust content tone"""
        improvements = []
        
        if tone == 'professional':
            # Replace casual words with professional ones
            replacements = {
                'awesome': 'excellent',
                'great': 'outstanding',
                'good': 'superior',
                'nice': 'impressive'
            }
            for casual, professional in replacements.items():
                text = text.replace(casual, professional)
                improvements.append('Adjusted tone to professional')
        
        return text, improvements
    
    def _improve_clarity(self, text, content_type, tone):
        """Improve content clarity"""
        improvements = []
        
        # Break down long sentences
        sentences = text.split('. ')
        improved_sentences = []
        
        for sentence in sentences:
            if len(sentence.split()) > 20:  # Long sentence
                improved_sentences.extend(self._break_long_sentence(sentence))
                improvements.append('Improved sentence clarity')
            else:
                improved_sentences.append(sentence)
        
        text = '. '.join(improved_sentences)
        return text, improvements
    
    def _apply_content_structure(self, text, content_type, tone):
        """Apply content type specific structure"""
        improvements = []
        
        if content_type in self.content_templates:
            structure = self.content_templates[content_type]['structure']
            
            # Ensure all structural elements are present
            for element in structure:
                if not self._has_element(text, element):
                    text = self._add_structural_element(text, element, content_type, tone)
                    improvements.append(f'Added {element.replace("_", " ")}')
        
        return text, improvements
    
    def _generate_structured_content(self, keywords, content_type, tone, length):
        """Generate content with specific structure"""
        template = self.content_templates[content_type]
        structure = template['structure']
        
        content_parts = []
        
        for element in structure:
            part = self._generate_element_content(element, keywords, tone, length)
            content_parts.append(part)
        
        return ' '.join(content_parts)
    
    def _generate_generic_content(self, keywords, tone, length):
        """Generate generic content from keywords"""
        # Create a compelling introduction
        intro = f"Discover the exceptional {keywords[0] if keywords else 'solution'} that transforms your experience."
        
        # Add benefits
        benefits = []
        for keyword in keywords[:3]:  # Use first 3 keywords
            benefits.append(f"• Premium {keyword} for optimal results")
            benefits.append(f"• Expert {keyword} solutions tailored to your needs")
            benefits.append(f"• Advanced {keyword} technology for superior performance")
        
        # Add call to action
        cta = "Experience the difference today and elevate your standards with our professional services."
        
        content = f"{intro} {' '.join(benefits)} {cta}"
        
        # Adjust length
        if length == 'short':
            content = content[:200]
        elif length == 'long':
            content = content + " " + self._expand_content(keywords, tone)
        
        return content
    
    def _create_variation(self, base_content, content_type, variation_index):
        """Create a variation of the base content"""
        variations = [
            self._rephrase_content,
            self._restructure_content,
            self._expand_content,
            self._condense_content
        ]
        
        variation_func = variations[variation_index % len(variations)]
        return variation_func(base_content, content_type)
    
    def _rephrase_content(self, content, content_type):
        """Rephrase content with different wording"""
        # Simple synonym replacement
        synonyms = {
            'excellent': ['outstanding', 'superior', 'exceptional', 'premium'],
            'quality': ['high-grade', 'superior', 'premium', 'excellent'],
            'professional': ['expert', 'skilled', 'qualified', 'experienced'],
            'service': ['solution', 'offering', 'expertise', 'capability']
        }
        
        for word, alternatives in synonyms.items():
            if word in content.lower():
                replacement = random.choice(alternatives)
                content = content.replace(word, replacement)
                break
        
        return content
    
    def _restructure_content(self, content, content_type):
        """Restructure content with different organization"""
        sentences = content.split('. ')
        if len(sentences) > 2:
            # Move the last sentence to the beginning
            last_sentence = sentences.pop()
            sentences.insert(0, last_sentence)
            content = '. '.join(sentences)
        
        return content
    
    def _expand_content(self, content, content_type):
        """Expand content with additional details"""
        expansion = " Our commitment to excellence ensures that every aspect of our service exceeds expectations. With years of experience and dedication to quality, we provide solutions that deliver measurable results and lasting value."
        return content + expansion
    
    def _condense_content(self, content, content_type):
        """Condense content to key points"""
        sentences = content.split('. ')
        if len(sentences) > 2:
            # Keep only the first and last sentences
            content = sentences[0] + '. ' + sentences[-1]
        
        return content
    
    def _calculate_keyword_density(self, content, keyword):
        """Calculate keyword density in content"""
        word_count = len(content.split())
        keyword_count = content.lower().count(keyword.lower())
        return keyword_count / word_count if word_count > 0 else 0
    
    def _calculate_seo_score(self, content, keywords):
        """Calculate SEO score based on various factors"""
        score = 0
        
        # Keyword presence
        for keyword in keywords:
            if keyword.lower() in content.lower():
                score += 20
        
        # Content length
        word_count = len(content.split())
        if 100 <= word_count <= 500:
            score += 20
        elif 500 < word_count <= 1000:
            score += 15
        
        # Readability (simple check)
        sentences = content.split('. ')
        avg_sentence_length = sum(len(s.split()) for s in sentences) / len(sentences)
        if 10 <= avg_sentence_length <= 20:
            score += 20
        
        return min(score, 100)
    
    def _add_seo_elements(self, content, keywords):
        """Add SEO-friendly elements to content"""
        # Add keywords naturally
        for keyword in keywords:
            if keyword.lower() not in content.lower():
                # Insert keyword in a natural way
                content = self._insert_keyword_naturally(content, keyword)
        
        return content
    
    def _insert_keyword_naturally(self, content, keyword):
        """Insert keyword naturally into content"""
        sentences = content.split('. ')
        if sentences:
            # Insert in the middle sentence
            mid_index = len(sentences) // 2
            sentences[mid_index] = f"{keyword} is a key component of our approach. " + sentences[mid_index]
            content = '. '.join(sentences)
        
        return content
    
    def _calculate_improvement_percentage(self, original, enhanced):
        """Calculate improvement percentage"""
        original_words = len(original.split())
        enhanced_words = len(enhanced.split())
        
        if original_words > 0:
            return ((enhanced_words - original_words) / original_words) * 100
        return 0
    
    def _get_variation_style(self, index):
        """Get variation style name"""
        styles = ['Rephrased', 'Restructured', 'Expanded', 'Condensed']
        return styles[index % len(styles)]
    
    def _has_element(self, text, element):
        """Check if text has specific element"""
        element_indicators = {
            'compelling_headline': ['introducing', 'discover', 'experience', 'premium'],
            'key_features': ['features', 'benefits', 'advantages', 'highlights'],
            'call_to_action': ['contact', 'call', 'visit', 'get started', 'learn more']
        }
        
        if element in element_indicators:
            indicators = element_indicators[element]
            return any(indicator in text.lower() for indicator in indicators)
        
        return True  # Assume present if no specific check
    
    def _add_structural_element(self, text, element, content_type, tone):
        """Add missing structural element"""
        element_additions = {
            'compelling_headline': f"Discover our exceptional {content_type.replace('_', ' ')} solutions.",
            'key_features': "Key features include premium quality, expert service, and guaranteed satisfaction.",
            'call_to_action': "Contact us today to experience the difference."
        }
        
        if element in element_additions:
            return text + " " + element_additions[element]
        
        return text
    
    def _generate_element_content(self, element, keywords, tone, length):
        """Generate content for specific element"""
        element_generators = {
            'compelling_headline': lambda: f"Experience exceptional {keywords[0] if keywords else 'quality'}",
            'key_features': lambda: f"Features include premium {', '.join(keywords[:2]) if len(keywords) >= 2 else 'quality'}",
            'benefits': lambda: f"Benefits: superior performance, expert service, guaranteed satisfaction",
            'call_to_action': lambda: "Contact us today to get started."
        }
        
        if element in element_generators:
            return element_generators[element]()
        
        return f"Professional {element.replace('_', ' ')} content."
    
    def _insert_modifier(self, text, modifier):
        """Insert modifier into text naturally"""
        sentences = text.split('. ')
        if sentences:
            # Insert in first sentence
            sentences[0] = f"{modifier.title()} {sentences[0].lower()}"
            text = '. '.join(sentences)
        
        return text
    
    def _create_paragraphs(self, sentences):
        """Create proper paragraph structure"""
        if len(sentences) <= 2:
            return '. '.join(sentences)
        
        # Group sentences into paragraphs
        paragraphs = []
        current_paragraph = []
        
        for i, sentence in enumerate(sentences):
            current_paragraph.append(sentence)
            
            # Start new paragraph every 2-3 sentences
            if len(current_paragraph) >= 2 and i < len(sentences) - 1:
                paragraphs.append('. '.join(current_paragraph))
                current_paragraph = []
        
        if current_paragraph:
            paragraphs.append('. '.join(current_paragraph))
        
        return ' '.join(paragraphs)
    
    def _break_long_sentence(self, sentence):
        """Break long sentence into shorter ones"""
        words = sentence.split()
        if len(words) <= 20:
            return [sentence]
        
        # Split at natural break points
        break_points = ['and', 'or', 'but', 'however', 'therefore']
        parts = []
        current_part = []
        
        for word in words:
            current_part.append(word)
            if word.lower() in break_points and len(current_part) > 10:
                parts.append(' '.join(current_part))
                current_part = []
        
        if current_part:
            parts.append(' '.join(current_part))
        
        return parts if parts else [sentence]

def main():
    """Main function to handle command line arguments"""
    if len(sys.argv) < 3:
        print(json.dumps({'error': 'Invalid arguments'}))
        return
    
    enhancer = ContentEnhancer()
    command = sys.argv[1]
    
    if command == 'enhance':
        if len(sys.argv) >= 5:
            original_text = sys.argv[2]
            content_type = sys.argv[3]
            tone = sys.argv[4]
            result = enhancer.enhance_content(original_text, content_type, tone)
        else:
            result = {'error': 'Missing arguments for enhance command'}
    
    elif command == 'generate':
        if len(sys.argv) >= 6:
            keywords = sys.argv[2]
            content_type = sys.argv[3]
            tone = sys.argv[4]
            length = sys.argv[5]
            result = enhancer.generate_content(keywords, content_type, tone, length)
        else:
            result = {'error': 'Missing arguments for generate command'}
    
    elif command == 'seo':
        if len(sys.argv) >= 4:
            content = sys.argv[2]
            target_keywords = json.loads(sys.argv[3])
            result = enhancer.optimize_for_seo(content, target_keywords)
        else:
            result = {'error': 'Missing arguments for seo command'}
    
    elif command == 'variations':
        if len(sys.argv) >= 5:
            base_content = sys.argv[2]
            content_type = sys.argv[3]
            count = int(sys.argv[4])
            result = enhancer.generate_variations(base_content, content_type, count)
        else:
            result = {'error': 'Missing arguments for variations command'}
    
    else:
        result = {'error': f'Unknown command: {command}'}
    
    print(json.dumps(result))

if __name__ == "__main__":
    main()

