<?php

namespace App\DDD\Modules\Approval\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRule extends Model
{
    protected $fillable = [
        'scenario_id', 'rule_type', 'field_name', 'operator', 'value', 'priority', 'is_active'
    ];

    protected $casts = [
        'value' => 'array',
        'is_active' => 'boolean'
    ];

    public function scenario()
    {
        return $this->belongsTo(ApprovalScenario::class, 'scenario_id');
    }

    /**
     * Kuralın geçerli olup olmadığını kontrol et
     */
    public function evaluate($data): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $fieldValue = $data[$this->field_name] ?? null;
        $ruleValue = $this->value;

        switch ($this->operator) {
            case 'equals':
                return $fieldValue == $ruleValue;
            
            case 'not_equals':
                return $fieldValue != $ruleValue;
            
            case 'greater_than':
                return $fieldValue > $ruleValue;
            
            case 'less_than':
                return $fieldValue < $ruleValue;
            
            case 'between':
                return $fieldValue >= $ruleValue[0] && $fieldValue <= $ruleValue[1];
            
            case 'in':
                return in_array($fieldValue, $ruleValue);
            
            case 'not_in':
                return !in_array($fieldValue, $ruleValue);
            
            default:
                return false;
        }
    }

    /**
     * Kural açıklamasını getir
     */
    public function getDescription(): string
    {
        $operators = [
            'equals' => 'eşittir',
            'not_equals' => 'eşit değildir',
            'greater_than' => 'büyüktür',
            'less_than' => 'küçüktür',
            'between' => 'arasında',
            'in' => 'içinde',
            'not_in' => 'içinde değil'
        ];

        $operator = $operators[$this->operator] ?? $this->operator;
        $value = is_array($this->value) ? implode(', ', $this->value) : $this->value;

        return "{$this->field_name} {$operator} {$value}";
    }
} 